<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use App\Models\UserFamilyDnaReport;
use App\Http\Controllers\Controller;

class UserDnaReportController extends Controller
{


    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:user_family_members,id',
            'report' => 'required|file|mimes:pdf,json,txt|max:2048',
        ]);

        $file = $request->file('report');
        $filePath = Helper::fileUpload($file, 'DNA', getFileName($file));

        $reportData = $this->parsePdf(storage_path('app/public/' . $file));
        dd($reportData);

        $report = UserFamilyDnaReport::create([
            'user_family_member_id' => $request->member_id,
            'file_path' => $filePath,
            'report_data' => json_encode($reportData) 
        ]);

        return response()->json(['success' => true, 'report_id' => $report->id]);
    }





    protected function parsePdf($fileFullPath)
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($fileFullPath);
            $text = $pdf->getText();

            $parsedData = [];

            // Simple parsing example: Trait: Value
            foreach (explode("\n", $text) as $line) {
                if (preg_match('/^(.*?):\s*(.*?)$/', trim($line), $matches)) {
                    $parsedData[$matches[1]] = $matches[2];
                }
            }

            return $parsedData;
        } catch (\Exception $e) {
            return null; // or [] or throw if needed
        }
    }
}
