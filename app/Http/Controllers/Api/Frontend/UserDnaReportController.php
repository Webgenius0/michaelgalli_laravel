<?php

namespace App\Http\Controllers\Api\Frontend;


use App\Helpers\Helper;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\UserFamilyDnaReport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class UserDnaReportController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'report' => 'required|mimes:pdf|max:10000',
        ]);

        try {
            // 1. Store PDF in storage/app/pdfs
            $file = $request->file('report');
            $storedPath = $file->store('uploads', 'public');
            $fullPath = storage_path('app/public/' . $storedPath);
            // dd($fullPath);

            // 2. Ensure file exists
            // if (!file_exists($fullPath)) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => "File not found at: $fullPath",
            //         'code' => 500,
            //         't-errors' => [],
            //     ]);
            // }

            // 3. Parse PDF text
            $parser = new Parser();
            $pdf = $parser->parseFile($fullPath);
            $text = $pdf->getText();

            // dd($text);

            // 4. Call OpenAI
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You will receive unstructured PDF text and must convert it into clean, structured JSON data. Be consistent with JSON keys.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $text,
                    ],
                ],
            ]);





            $structuredJson = json_decode($response['choices'][0]['message']['content'], true);

            $reports = [];
            if (isset($structuredJson['DNA_Test_Report']) && is_array($structuredJson['DNA_Test_Report'])) {
                foreach ($structuredJson['DNA_Test_Report'] as $traitData) {
                    $reports[] = UserFamilyDnaReport::create([
                        'user_family_member_id' => $request->member_id,
                        'file_path' => $storedPath,
                        'report_data' => json_encode($traitData),
                        'trait' => $traitData['Trait'] ?? null,
                        'value' => $traitData['Result'] ?? null,
                    ]);
                }
            } else {
                // fallback for single trait structure
                $reports[] = UserFamilyDnaReport::create([
                    'user_family_member_id' => $request->member_id,
                    'file_path' => $storedPath,
                    'report_data' => json_encode($structuredJson),
                    'trait' => $structuredJson['trait'] ?? null,
                    'value' => $structuredJson['value'] ?? null,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'PDF processed successfully.',
                'data' => $reports,
            ]);
            // ...existing code...














            return response()->json([
                'status' => true,
                'message' => 'PDF processed successfully.',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error processing PDF.',
                'code' => 500,
                't-errors' => [$e->getMessage()],
            ]);
        }
    }
}
