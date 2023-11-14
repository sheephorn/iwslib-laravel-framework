<?php

namespace IwslibLaravel\Http;

interface IParam
{
    public function setData(array $data);
    public function rules(): array;
    public function toArray(): array;
}
