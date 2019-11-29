<?php
namespace CoruscateSolutions\SerialNumberGeneratorLaravel\Helpers;

use CoruscateSolutions\SerialNumberGeneratorLaravel\Models\SerialNoGenerator;

class SerialNoGeneratorHelper {


    public function getSerialNumber($type){

       /*  $setting = SerialNoGenerator::where(['type'=>$type,'is_active'=>true])
        ->first(); */

        $setting =  \DB::getCollection('serial_no_generators')->findOneAndUpdate(
            ['type'=>$type,'is_active'=>true],
            ['$inc' => ['total_no' => 1]],
            ['new' => true, 'returnDocument' => \MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER]
        );

        //get the name of setting number generator type
        if (!$setting) {
            return response()->json([
                'message'=>'Given type not found'
            ]);
        }

        //generate serial number using given setting number generator
        //also, save in the database if save is given as true
        return $this->generateSerialNo($setting);
    }


    /**
     * Function to generate the serial Number.
     * Returns the incremented value of total_no
     * prefix + (start_from + total_no) + (postfix ?? '') + financial_year
     * @param $setting
     * @return array
     */
    public function generateSerialNo($serial)
    {
       
        $number = '';

        $prefix = $serial->prefix ?? '';
        $postfix = $serial->postfix ?? '';
        $financialYear = $serial->financial_year ?? '';
        $startFrom = $serial->start_from ?? 0;

        $digit =  $startFrom + $serial->total_no;

        $number = $prefix . $digit . $postfix . $financialYear;

        return $number;
    }
}
