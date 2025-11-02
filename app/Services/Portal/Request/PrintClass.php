<?php

namespace App\Services\Portal\Request;
use Hashids\Hashids;
use App\Models\OrgChart;
use App\Models\RequestReport;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class PrintClass
{
    public function document($request)
    {
        $hashids = new Hashids('krad', 10);
        $id = $hashids->decode($request->key);

        $data = RequestReport::where('request_id', $id)->value('information');
        $data = json_decode($data,true);
        $url = $_SERVER['HTTP_HOST'] . '/verification/' . $request->key;
        $qrCode = new QrCode($url);
        $qrCode->setSize(300);
        $pngWriter = new PngWriter();
        $qrCodeImageString = $pngWriter->write($qrCode)->getString();
        $base64Image = 'data:image/png;base64,' . base64_encode($qrCodeImageString);

        $array = [
            'qrCodeImage' => $base64Image,
            'data' => $data,
            'signatory' => $this->signatory($data['divisions'])
        ];

        switch($request->type){
            case 'overtime':
                $pdf = \PDF::loadView('reports.overtime', $array)->setPaper('a4', 'portrait');
            break;
            case 'leave':
                $pdf = \PDF::loadView('reports.leave', $array)->setPaper('a4', 'portrait');
            break;
        }

        $pdf->output();
        $dompdf = $pdf->getDomPDF();

        $canvas = $dompdf->getCanvas();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "PAGE $pageNumber OF $pageCount";
            $font = $fontMetrics->get_font("Helvetica", "normal");
            $canvas->text(63.5, 796, $text, $font, 7);
        });

        $pdfBinary = $dompdf->output();
        $secret = config('app.key');
        $hmac = hash_hmac('sha256', $pdfBinary, $secret);
        $meta = "\n%--- DOC META ---\n";
        $meta .= "% ValidationHMAC: {$hmac}\n";
        $meta .= "% GeneratedAt: " . now()->toDateTimeString() . "\n";
        $meta .= "%--- END META ---\n";

        $pos = strrpos($pdfBinary, '%%EOF');
        if ($pos !== false) {
            $pdfBinary = substr_replace($pdfBinary, $meta . '%%EOF', $pos, 5);
        } else {
            $pdfBinary .= $meta . "%%EOF\n";
        }

        return response($pdfBinary)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="'.$data['code'].'"');
    }

    private function signatory($divisions){
        $a = OrgChart::with('user.profile','oic.profile')->where('designation_id',43)->where('is_active',1)->first(); 
        $approved = [
            'name' => ($a->is_oic) ? $a->oic->profile->fullname : $a->user->profile->fullname,    
            'role' => ($a->is_oic) ? 'OIC - Regional Director' : 'Regional Director'
        ];
        foreach($divisions as $division){
            $d = OrgChart::with('user.profile','oic.profile','assigned')
            ->whereHas('assigned', function ($query) use ($division){
                $query->where('name', $division);
            })
            ->where('designation_id',44)->where('is_active',1)->first(); 
            if ($d) {
                $assigned = $d->assigned->others ?? '';
                $recommended[] = [
                    'name' => ($d->is_oic) ? $d->oic->profile->fullname : $d->user->profile->fullname,
                    'role' => ($d->is_oic) ? 'OIC - Assistant Regional Director (' . $assigned . ')' : 'Assistant Regional Director (' . $assigned . ')'
                ];
            } else {
                $recommended[] = [
                    'name' => '',
                    'role' => ''
                ];
            }
        }
        return [
            'approved' => $approved,
            'recommended' => $recommended
        ];
    }
}
