<?php

namespace App\Http\Controllers;

use App\ResponseUtil\ResponseUtil;
use Response;

/**
 * @SWG\Swagger(
 *   basePath="/api/v1",
 *   @SWG\Info(
 *     title="Laravel Generator APIs",
 *     version="1.0.0",
 *   )
 * )
 * This class should be parent class for other API controllers
 * Class AppBaseController
 */
class AppBaseController extends Controller
{
    /**
     * @param $result
     * @param $message
     * @return mixed
     */
    public function sendResponse($result, $message)
    {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }

    /**
     * @param $error
     * @param  int  $code
     * @return mixed
     */
    public function sendError($error, $code = 404)
    {
        return Response::json(ResponseUtil::makeError($error), $code);
    }

    /**
     * @param $message
     * @return mixed
     */
    public function sendSuccess($message)
    {
        return Response::json([
            'success' => true,
            'message' => $message,
        ], 200);
    }

    function amountToWords($amount, $language = 'en')
    {
        $number = number_format($amount, 2, '.', ''); // Format to 2 decimal places
        list($whole, $decimal) = explode('.', $number);

        $whole = (int) $whole;  // Whole number part
        $decimal = (int) $decimal; // Decimal part

        $words = '';

        if ($language === 'ar') {
            if ($whole > 0) {
                $words .= $this->convertNumberToWords($whole, 'ar') . ' ريال سعودي';
            } else {
                $words .= 'صفر ريال سعودي';
            }

            if ($decimal > 0) {
                $words .= ' و ' . $this->convertNumberToWords($decimal, 'ar') . ' هللة';
            } else {
                $words .= ' و صفر هللة';
            }
        } else {
            if ($whole > 0) {
                $words .= $this->convertNumberToWords($whole, 'en') . ' Saudi Riyals';
            } else {
                $words .= 'zero Saudi Riyals';
            }

            if ($decimal > 0) {
                $words .= ' and ' . $this->convertNumberToWords($decimal, 'en') . ' Halalas';
            } else {
                $words .= ' and zero Halalas';
            }
        }

        return $words;
    }

    function convertNumberToWords($number, $language = 'en')
    {
        $dictionaryEn = [
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'forty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            1000000 => 'million',
            1000000000 => 'billion',
        ];

        $dictionaryAr = [
            0 => 'صفر',
            1 => 'واحد',
            2 => 'اثنان',
            3 => 'ثلاثة',
            4 => 'أربعة',
            5 => 'خمسة',
            6 => 'ستة',
            7 => 'سبعة',
            8 => 'ثمانية',
            9 => 'تسعة',
            10 => 'عشرة',
            11 => 'أحد عشر',
            12 => 'اثنا عشر',
            13 => 'ثلاثة عشر',
            14 => 'أربعة عشر',
            15 => 'خمسة عشر',
            16 => 'ستة عشر',
            17 => 'سبعة عشر',
            18 => 'ثمانية عشر',
            19 => 'تسعة عشر',
            20 => 'عشرون',
            30 => 'ثلاثون',
            40 => 'أربعون',
            50 => 'خمسون',
            60 => 'ستون',
            70 => 'سبعون',
            80 => 'ثمانون',
            90 => 'تسعون',
            100 => 'مائة',
            1000 => 'ألف',
            1000000 => 'مليون',
            1000000000 => 'مليار',
        ];

        $dictionary = $language === 'ar' ? $dictionaryAr : $dictionaryEn;

        if ($number < 0) {
            return ($language === 'ar' ? 'سالب ' : 'negative ') . $this->convertNumberToWords(abs($number), $language);
        }

        if ($number < 21) {
            return $dictionary[$number];
        }

        if ($number < 100) {
            $ten = floor($number / 10) * 10;
            $unit = $number % 10;
            return $unit ? $dictionary[$ten] . ($language === 'ar' ? ' و ' : '-') . $dictionary[$unit] : $dictionary[$ten];
        }

        if ($number < 1000) {
            return $dictionary[floor($number / 100)] . ($language === 'ar' ? ' مائة' : ' hundred') . ($number % 100 ? ($language === 'ar' ? ' و ' : ' and ') . $this->convertNumberToWords($number % 100, $language) : '');
        }

        foreach (array_reverse($dictionary, true) as $key => $value) {
            if ($number >= $key) {
                return $this->convertNumberToWords(floor($number / $key), $language) . ' ' . $value . ($number % $key ? ' ' . $this->convertNumberToWords($number % $key, $language) : '');
            }
        }

        return '';
    }

}
