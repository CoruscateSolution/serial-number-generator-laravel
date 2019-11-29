<?php

namespace CoruscateSolutions\SerialNumberGeneratorLaravel\Http\Controllers;

use CoruscateSolutions\SerialNumberGeneratorLaravel\Http\Requests\SerialStoreRequest;
use CoruscateSolutions\SerialNumberGeneratorLaravel\Models\SerialNoGenerator;
use Exception;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;


class SerialController extends Controller
{

    public function __construct()
    { }

    /**
     * add Setting
     * @param SerialStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SerialStoreRequest $request)
    {


        try {
            $input = $request->all();

            if (isset($input['date_from']) && isset($input['date_to'])) {
                $carbonFromDate = Carbon::parse($input['date_from']);

                $carbonToDate = Carbon::parse($input['date_to']);

                $input['is_active'] = false;
                //if current date is in between the from and end date, in that case only, is_active status will be true 
                if (Carbon::now()->between($carbonFromDate, $carbonToDate)) {
                    $input['is_active'] = true;
                }

                //check whether the date range conflicts with another number generator of same type
                $dateInput = [];
                $dateInput['date_from'] = $input['date_from'];
                $dateInput['date_to'] = $input['date_to'];
                $dateInput['type'] = $input['type'];
                $numberGenerators = SerialNoGenerator::where($dateInput)->get();

                if ($numberGenerators && count($numberGenerators) > 0) {

                    return response()->json([
                        'data' => null,
                        'message' => 'There is already a number generator exists in given date range.',
                        'code' => 422
                    ]);
                }

                $input['date_from'] = $this->isoDateToUTC($input['date_from']);
                $input['date_to'] = $this->isoDateToUTC($input['date_to']);
            }
            if (isset($input['is_active']) && $input['is_active'] == true) {
                //if the active number generator of the same type is already there, inactivate that
                $updateSerialNo = SerialNoGenerator::where('is_active', true)
                    ->where('type', $input['type']);

                if (isset($input['_id'])) {
                    $updateSerialNo->where('_id', '<>', $input['_id']);
                }
                $updateSerialNo->update(['is_active' =>  false]);
            }

            //add total_no as 0
            $input['total_no'] = 0;

            $settingNoGenerator = SerialNoGenerator::create($input);
            return response()->json([
                'data' => $settingNoGenerator,
                'message' => 'The serial number generator created successfully.',
                'code' => 201
            ]);
        } catch (\Exception $exception) {
            \Log::error($exception);
            return response()->json([
                'data' => null,
                'message' => 'Something went wrong ,please try again later',
                'code' => 422
            ]);
        }
    }

    /**
     * Show given serialNoGenerator
     * @param SerialNoGenerator $serialNoGenerator
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $serialNoGenerator = SerialNoGenerator::find($id);

        if ($serialNoGenerator) {
            return response()->json([
                'data' => $serialNoGenerator,
                'message' => 'The number generator retrieved successfully',
                'code' => 200
            ]);
        }

        return response()->json([
            'data' => null,
            'message' => 'The number generator not found',
            'code' => 422
        ]);
    }


    /**
     * Get list of all the SettingNoGenerator
     * Also get filter list by $search
     *
     * @param null $search
     * @return \Illuminate\Http\JsonResponse
     */
    public function listDetail(Request $request)
    {
        $input = $request->all();
        $settingNoGenerators = SerialNoGenerator::where($input)->get();

        if (count($settingNoGenerators) > 0) {

            return response()->json([
                'data' =>
                ['list' => $settingNoGenerators],
                'message' => 'The number generator retrieved successfully',
                'code' => 200
            ]);
        }

        return response()->json([
            'data' => null,
            'message' => 'The number generator not found',
            'code' => 422
        ]);
    }



    /**
     * Delete given SettingNoGenerator
     *
     * @param SettingNoGenerator $settingNoGenerator
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {

        $serialNoGenerator = SerialNoGenerator::find($id);

        if (!$serialNoGenerator) {
            return response()->json([
                'data' => null,
                'message' => 'The number generator not found',
                'code' => 422
            ]);
        }

        //check whether the generator is used or not, if yes, it cannot be deleted
        if ($serialNoGenerator->total_no > 0) {

            return response()->json([
                'data' => null,
                'message' => "The number generator cannot be deleted as it is already used for {$serialNoGenerator->total_no} times in number generation process.",
                'code' => 422
            ]);
        }

        //also if the number generator is the only active number generator, it cannot be deleted
        $dateInput['type'] = $serialNoGenerator->type;
        $dateInput['is_active'] = true;
        $serialNoGenerators = SerialNoGenerator::where($dateInput)->where('_id', '<>', $id)->count();

        if (!$serialNoGenerators || count($serialNoGenerators) == 0) {

            return response()->json([
                'data' => null,
                'message' => "There must be at least one active record in number generator.",
                'code' => 422
            ]);
        }

        //delete the given number generator
        $serialNoGenerator->delete();
        return response()->json([
            'data' => null,
            'message' => "The number generator deleted successfully.",
            'code' => 200
        ]);
    }

    /**
     * Function used to create ISO Date to UTC
     * @param $date
     * @return \MongoDB\BSON\UTCDateTime
     */
    public function isoDateToUTC($date)
    {

        $origDate = new \DateTime($date);
        $origDate = $origDate->getTimestamp() * 1000;
        $dt = new \MongoDB\BSON\UTCDateTime($origDate);
        return $dt;
    }
}
