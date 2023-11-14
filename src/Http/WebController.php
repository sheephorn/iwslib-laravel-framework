<?php

namespace IwslibLaravel\Http;

use IwslibLaravel\Codes\EnvironmentName;
use IwslibLaravel\Codes\HTTPResultCode as ResultCode;
use IwslibLaravel\Exceptions\AppCommonException;
use IwslibLaravel\Exceptions\ExclusiveException;
use IwslibLaravel\Exceptions\GeneralErrorMessageException;
use IwslibLaravel\Util\DBUtil;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use LogicException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class WebController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const COL_NAME_CREATED_AT = 'created_at';
    const COL_NAME_UPDATED_AT = 'updated_at';

    const COL_NAME_RESULT_CODE = 'result';
    const COL_NAME_DATA = 'data';
    const COL_NAME_MESSAGES = 'messages';
    const COL_NAME_GENERAL_MESSAGE = 'general';
    const COL_NAME_EMAIL_ID = 'email_id';
    const COL_NAME_ERRORS = 'errors';

    /**
     * バリデートした結果を格納
     *
     * @var array
     */
    protected $validated = [];

    /**
     * 画面へ返却するメールID
     *
     * @var integer|null
     */
    private int|null $emailId = null;

    /**
     * 返却するメッセージ
     *
     * @var array|null
     */
    private array|null $messages = null;

    /**
     * 返却するメッセージ
     *
     * @var string|null
     */
    private string|null $generalMessage = null;

    /**
     * 返却するデータ
     *
     * @var mixed|null
     */
    private $data = null;

    protected DBUtil $transaction;

    /**
     * 返却する結果コード
     *
     * @var ResultCode|null
     */
    protected ResultCode|null $resultCode = ResultCode::SECCESS;

    public function __construct()
    {
        $this->transaction = DBUtil::instance();
    }


    /**
     * パラメータオブジェクト
     */
    protected function getParam(): IParam
    {
        if (!property_exists(static::class, 'param')) {
            throw new LogicException("param未定義");
        }

        $param = $this->param;

        if (!is_subclass_of($param, IParam::class)) {
            throw new LogicException("param型不正");
        }
        return $this->param;
    }


    /**
     * コントローラーの名前
     * オーバーライドされることを想定
     * 主に、Routeのドキュメント作成用
     *
     * @return string
     */
    public function name(): string
    {
        return "---未設定---";
    }

    /**
     * コントローラーの説明
     * オーバーライドされることを想定
     * 主に、Routeのドキュメント作成用
     *
     * @return string
     */
    public function description(): string
    {
        return "---未設定---";
    }

    /**
     * オーバーライド必要
     * メインロジック
     *
     * @param Request $request
     * @return  Response|JsonResponse|string
     */
    protected function run(Request $request): Response|JsonResponse|BinaryFileResponse|ClientResponse |string
    {
        return $this->successResponse();
    }

    private function getRules()
    {
        return $this->getParam()->rules();
    }

    public function entry(Request $request)
    {
        $this->setLogContext($request);

        try {
            $validator = Validator::make($request->all(), $this->getRules());
            $validator->validate();
        } catch (ValidationException $e) {
            logger("validate error", ['errors' => $e->errors(), 'request' => $request->all(), 'path' =>  $request->path()]);
            logger($request->toArray());
            return $this->validateErrorResponse($e);
        }

        try {
            $this->validated = $validator->validated();
            $this->getParam()->setData($this->validated);

            $this->authorize();

            $this->transaction->beginTransaction();
            $ret =  $this->run($request);

            $this->transaction->commit();
            return $ret;
        } catch (GeneralErrorMessageException $e) {
            $this->transaction->rollBack();
            return $this->failedResponse([], $e->getMessage());
        } catch (AppCommonException $e) {
            $this->transaction->rollBack();
            logs()->error(sprintf("Appエラー:%s File:%s Line:%d", $e->getMessage(), $e->getFile(), $e->getLine()));
            return $this->failedResponse();
        } catch (ExclusiveException $e) {
            $this->transaction->rollBack();
            logs()->error(sprintf("排他エラー:%s", $e->getMessage()));
            return $this->exclusiveErrorResponse();
        } catch (LogicException $e) {
            $this->transaction->rollBack();
            logs()->error([
                sprintf("実装エラー:%s", $e->getMessage()),
                get_class($e),
                $e->getFile(),
                $e->getLine(),
                $request->all(),
            ]);
            logger(array_filter($e->getTrace(), function ($val, $key) {
                return $key <= 5;
            }, ARRAY_FILTER_USE_BOTH));
            return $this->failedResponse();
        } catch (ValidationException $e) {
            $this->transaction->rollBack();
            return $this->validateErrorResponse($e);
        } catch (HttpException $e) {
            $this->transaction->rollBack();
            if ($e->getStatusCode() === 401) {
                return $this->unAuthorizedResponse();
            }
            throw $e;
        } catch (Exception $e) {
            $this->transaction->rollBack();
            logs()->error([
                sprintf("例外エラー:%s", $e->getMessage()),
                get_class($e),
                $e->getFile(),
                $e->getLine(),
                $request->all(),
            ]);
            logger(array_filter($e->getTrace(), function ($val, $key) {
                return $key <= 5;
            }, ARRAY_FILTER_USE_BOTH));
            return $this->failedResponse();
        }
    }

    protected function successResponse(array|object $data = [], array|string $messages = [])
    {
        return $this->setData($data)
            ->setMessages($messages)
            ->setResultCode(ResultCode::SECCESS)
            ->makeResponse();
    }

    protected function failedResponse(array|object $data = [], array|string $messages = [])
    {
        return $this->setData($data)
            ->setMessages($messages)
            ->setResultCode(ResultCode::FAILED)
            ->makeResponse();
    }
    protected function unAuthorizedResponse(array|object $data = [], array|string $messages = [])
    {
        return $this->setData($data)
            ->setMessages($messages)
            ->setResultCode(ResultCode::UNAUTHORIZED)
            ->makeResponse();
    }
    protected function exclusiveErrorResponse(array|object $data = [], array|string $messages = [])
    {
        return $this->setData($data)
            ->setMessages($messages)
            ->setResultCode(ResultCode::EXCLUSIVE_ERROR)
            ->makeResponse();
    }

    protected function validateErrorResponse(ValidationException|array $exception, string|null $generalMessage = null)
    {

        $errorMessages = [];
        $general = null;
        if ($exception instanceof ValidationException) {
            foreach ($exception->errors() as $key => $m) {
                $errorMessages[$key] = $m[0];
            }
        }

        if (is_array($exception)) {
            $errorMessages = $exception;
        }

        $general = $generalMessage ?? data_get($errorMessages, self::COL_NAME_GENERAL_MESSAGE);

        return $this->setData([])
            ->setMessages($errorMessages)
            ->setGeneralMessage($general)
            ->setResultCode(ResultCode::FAILED)
            ->makeResponse();
    }

    protected function makeResponse()
    {
        if ($this->resultCode === null) {
            abort(403);
        }

        $ret = [];
        Arr::set($ret, self::COL_NAME_RESULT_CODE, $this->resultCode->value);
        if ($this->data !== null) {
            Arr::set($ret, self::COL_NAME_DATA, $this->data);
        }
        if ($this->messages !== null) {
            Arr::set($ret, self::COL_NAME_MESSAGES . "." . self::COL_NAME_ERRORS, $this->messages);
        }
        if ($this->generalMessage !== null) {
            Arr::set($ret, self::COL_NAME_MESSAGES . "." . self::COL_NAME_GENERAL_MESSAGE, $this->generalMessage);
        }
        if ($this->emailId !== null) {
            Arr::set($ret, self::COL_NAME_MESSAGES . "." . self::COL_NAME_EMAIL_ID, $this->emailId);
        }

        if (request()->wantsJson()) {
            return response()
                ->json($ret)
                ->withHeaders($this->makeHeader());
        } else {

            if (app()->environment([EnvironmentName::PRODUCTOIN->value])) {
                abort(500);
            }
            return response()
                ->json($ret)
                ->withHeaders($this->makeHeader());
        }
    }

    private function makeHeader(): array
    {
        $header = [];
        $user = Auth::user();
        if ($user) {
            $header["App-User-Auth"] = sprintf("%s", $user->id);
        } else {
            $header["App-User-Auth"] = 'none';
        }
        return $header;
    }

    // 以下　認可関係
    protected array|null $roleAllow = null;
    protected array|null $roleDisallow = null;
    protected array|null $customAllow = null;

    protected function roleAllow(UserRole $role)
    {
        $this->roleAllow = [];
        foreach (UserRole::cases() as $ele) {
            if ($role->value <= $ele->value) {
                $this->roleAllow[] = $ele;
            }
        }
    }

    private function authorize()
    {
        if (!Auth::check()) {
            return;
        }
    }

    // 返却用データの登録
    protected function setEmailId(int $emailId)
    {
        $this->emailId = $emailId;
        return $this;
    }

    protected function setMessages(array|string $messages)
    {
        if (is_array($messages)) {
            $this->messages = $messages;
        } else {
            $this->setGeneralMessage($messages);
        }
        return $this;
    }

    protected function setGeneralMessage(string|null $generalMessage)
    {
        $this->generalMessage = $generalMessage;
        return $this;
    }

    protected function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    protected function setResultCode(ResultCode $resultCode)
    {
        $this->resultCode = $resultCode;
        return $this;
    }

    protected function setLogContext(Request $request)
    {
        Log::withContext([
            '__requestUuid__' => strval(Str::uuid()),
            '__userId__' => Auth::id(),
            '__path__' => $request->path(),
        ]);
    }
}
