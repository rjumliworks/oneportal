<?php

namespace App\Services\Portal\Request;

use Hashids\Hashids;
use App\Models\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class PrintClass
{
    public function overtime($request){
        $hashids = new Hashids('krad',10);
        $id = $hashids->decode($request->key);

        $data = Request::with([
            'tags.user:id',
            'tags.user.profile:user_id,firstname,middlename,lastname,avatar,suffix_id',
            'tags.user.organization.position',
            'type',
            'dates',
            'detail',
            'user:id',
            'overtime',
            'user.profile:user_id,firstname,middlename,lastname,avatar,suffix_id',
            'signatories.division','signatories.approved.profile','signatories.recommended.profile'
        ])
        ->where('id',$id)
        ->first();
        // dd($data);

        $url = $_SERVER['HTTP_HOST'].'/verification/'.$request->key;
        $qrCode = new QrCode($url);
        $qrCode->setSize(300);
        $pngWriter = new PngWriter();
        $qrCodeImageString = $pngWriter->write($qrCode)->getString();
        $base64Image = 'data:image/png;base64,' . base64_encode($qrCodeImageString);
       
        $array = [
            'qrCodeImage' => $base64Image,
            'data' => $data
        ]; 

        $pdf = \PDF::loadView('reports.overtime',$array)->setPaper('a4', 'portrait');
        $pdf->output();
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $copies = 1;
            $totalPagesPerCopy = $pageCount / $copies;
            $currentPageInCopy = ($pageNumber - 1) % $totalPagesPerCopy + 1;
            $text = "PAGE $currentPageInCopy OF $totalPagesPerCopy";
            $font = $fontMetrics->get_font("Helvetica", "normal");
            $size = 7;
            $width = $fontMetrics->get_text_width($text, $font, $size);
            $canvas->text(106 - $width, 796, $text, $font, $size);
        });
        return $pdf->stream('test.pdf');
    }
}
