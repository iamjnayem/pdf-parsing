<?php

namespace App\Services;

class PdfParserService
{
    public function parse($pdfPath)
    {
        $outputPath = "output.txt";
        $command = "pdftotext " . escapeshellarg($pdfPath) . " " . escapeshellarg($outputPath);
        
        exec($command, $output, $returnVar);
        
        if ($returnVar !== 0) {
            throw new \Exception("Error processing PDF file.");
        }

        $text = file_get_contents($outputPath);
        
        unlink($outputPath);

        return $this->parseText($text);
    }

    private function parseText($text)
    {
        $parsedData = [];
        // Regular expressions for each field
        $patterns = [
            'registration_date' => "REGISTRATION DATE(?:[^\n]*\n){5}\s*([\s\S]*?)\n",
            'registration_office' => 'REGISTRATION OFFICE(?:[^\n]*\n){5}\s*([\s\S]*?)\n',
            'issuance_date' => 'ISSUANCE DATE(?:[^\n]*\n){5}\s*([\s\S]*?)\n',
            'date_of_birth' => 'DATE OF BIRTH(?:[^\n]*\n){3}\s*([\s\S]*?)\n',
            'birth_registration_number' => 'BIRTH REGISTRATION NUMBER(?:[^\n]*\n){3}\s*([\s\S]*?)\n',
            'sex' => 'SEX\s*\n(.*?)\n',
            'registered_person_name_bangla' => 'নিবন্ধিত\s*ব্যক্তির নাম\s*\n\n([\s\S]*?)\n\n',
            'registered_person_name' => 'REGISTERED\s*PERSON NAME\s*\n\n([\s\S]*?)\n\n',
            'mother_name_bangla' => 'মাতার নাম\s*\n\n([\s\S]*?)\n\n',
            'mother_name' => 'MOTHER\'S NAME\s*\n\n([\s\S]*?)\n\n',
            'father_name_bangla' => 'পিতার নাম\s*\n\n([\s\S]*?)\n\n',
            'father_name' => 'FATHER\'S NAME\s*\n\n([\s\S]*?)\n\n',
            'mother_nationality_bangla' => 'মাতার\nজাতীয়তা\s*\n\n([\s\S]*?)\n\n',
            'mother_nationality' => 'MOTHER\'S\nNATIONALITY\s*\n\n([\s\S]*?)\n\n',
            'father_nationality_bangla' => 'পিতার\nজাতীয়তা\s*\n\n([\s\S]*?)\n\n',
            'father_nationality' => 'FATHER\'S\nNATIONALITY\s*\n\n([\s\S]*?)\n\n',
            'place_of_birth_bangla' => 'জন্মস্থান\s*\n\n([\s\S]*?)\n\n',
            'place_of_birth' => 'PLACE OF BIRTH\s*\n\n([\s\S]*?)\n\n',
            'location_of_registered_office' => 'Location of the Register office :\s*([\s\S]*?)\.\s*everify'
        ];

        foreach ($patterns as $key => $pattern) {
            if (preg_match("/$pattern/u", $text, $matches)) {
               
                $matches[1] = str_replace("\n", " ", $matches[1]);
                $parsedData[$key] = trim($matches[1]);
            }
        }
        
        return $parsedData;
    }
}

