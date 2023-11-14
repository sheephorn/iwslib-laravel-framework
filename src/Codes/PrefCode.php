<?php

namespace IwslibLaravel\Codes;

/**
 * JIS X 0401都道府県コード
 */
enum  PrefCode: string
{

    case HOKKAIDO = '01';
    case AOMORI = '02';
    case IWATE = '03';
    case MIYAGI = '04';
    case AKITA = '05';
    case YAMAGATA = '06';
    case FUKUSHIMA = '07';
    case IBARAGI = '08';
    case TOCHIGI = '09';
    case GUNMA = '10';
    case SAITAMA = '11';
    case CHIBA = '12';
    case TOKYO = '13';
    case KANAGAWA = '14';
    case NIGATA = '15';
    case TOYAMA = '16';
    case ISHIKAWA = '17';
    case FUKUI = '18';
    case YAMANASHI = '19';
    case NAGANO = '20';
    case GIFU = '21';
    case SHIZUOKA = '22';
    case AICHI = '23';
    case MIE = '24';
    case SHIGA = '25';
    case KYOTO = '26';
    case OSAKA = '27';
    case HYOGO = '28';
    case NARA = '29';
    case WAKAYAMA = '30';
    case TOTTORI = '31';
    case SHIMANE = '32';
    case OKAYAMA = '33';
    case HIROSHIMA = '34';
    case YAMAGUCHI = '35';
    case TOKUSHIMA = '36';
    case KAGAWA = '37';
    case EHIME = '38';
    case KOCHI = '39';
    case FUKUOKA = '40';
    case SAGA = '41';
    case NAGASAKI = '42';
    case KUMAMOTO = '43';
    case OITA = '44';
    case MIYAZAKI = '45';
    case KAGOSHIMA = '46';
    case OKINAWA = '47';

    static private function getDictionary()
    {
        return [
            self::HOKKAIDO->value => '北海道',
            self::AOMORI->value => '青森',
            self::IWATE->value => '岩手',
            self::MIYAGI->value => '宮城',
            self::AKITA->value => '秋田',
            self::YAMAGATA->value => '山形',
            self::FUKUSHIMA->value => '福島',
            self::IBARAGI->value => '茨城',
            self::TOCHIGI->value => '栃木',
            self::GUNMA->value => '群馬',
            self::SAITAMA->value => '埼玉',
            self::CHIBA->value => '千葉',
            self::TOKYO->value => '東京',
            self::KANAGAWA->value => '神奈川',
            self::NIGATA->value => '新潟',
            self::TOYAMA->value => '富山',
            self::ISHIKAWA->value => '石川',
            self::FUKUI->value => '福井',
            self::YAMANASHI->value => '山梨',
            self::NAGANO->value => '長野',
            self::GIFU->value => '岐阜',
            self::SHIZUOKA->value => '静岡',
            self::AICHI->value => '愛知',
            self::MIE->value => '三重',
            self::SHIGA->value => '滋賀',
            self::KYOTO->value => '京都',
            self::OSAKA->value => '大阪',
            self::HYOGO->value => '兵庫',
            self::NARA->value => '奈良',
            self::WAKAYAMA->value => '和歌山',
            self::TOTTORI->value => '鳥取',
            self::SHIMANE->value => '島根',
            self::OKAYAMA->value => '岡山',
            self::HIROSHIMA->value => '広島',
            self::YAMAGUCHI->value => '山口',
            self::TOKUSHIMA->value => '徳島',
            self::KAGAWA->value => '香川',
            self::EHIME->value => '愛媛',
            self::KOCHI->value => '高知',
            self::FUKUOKA->value => '福岡',
            self::SAGA->value => '佐賀',
            self::NAGASAKI->value => '長崎',
            self::KUMAMOTO->value => '熊本',
            self::OITA->value => '大分',
            self::MIYAZAKI->value => '宮崎',
            self::KAGOSHIMA->value => '鹿児島',
            self::OKINAWA->value => '沖縄',
        ];
    }

    static public function toArray()
    {
        $ret = [];
        $dic = self::getDictionary();
        foreach (self::cases() as $val) {
            if (isset($dic[$val->value])) {
                $ret[][$val->value] = $dic[$val->value];
            }
        }
        return $ret;
    }

    static public function getName(?PrefCode $code)
    {
        if ($code === null) return "";
        $dic = self::getDictionary();
        return data_get($dic, $code->value, "");
    }
}
