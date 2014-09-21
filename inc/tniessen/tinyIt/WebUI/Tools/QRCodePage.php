<?php
namespace tniessen\tinyIt\WebUI\Tools;

use tniessen\tinyIt\WebUI\Page;
use tniessen\tinyIt\HttpParams;
use tniessen\tinyIt\Application;

class QRCodePage extends Page
{
    private $errorMessage;

    private $currentParams;
    private $doGenerateCode;

    public function init($params)
    {
        self::requireLogin();

        if(!isset($params['qr-data'])) {
            $params['qr-data'] = Application::getBaseURL()->build();
        }

        $hParams = new HttpParams($params);
        $this->currentParams = $hParams;

        $this->doGenerateCode = $hParams->hasValues(array(
            'qr-data', 'qr-size', 'qr-fgcolor', 'qr-bgcolor'
        ));
    }

    public function render()
    {
        $opts = array();
        if($this->errorMessage !== null) {
            $opts['errorMessage'] = $this->errorMessage;
        }
        if($this->currentParams !== null) {
            foreach($this->currentParams->values() as $field => $value) {
                $opts['current:' . $field] = $value;
            }
        }
        if($this->doGenerateCode) {
            $qrurl = 'https://api.qrserver.com/v1/create-qr-code/?margin=0';
            $qrurl .= '&data=' . urlencode($this->currentParams->get('qr-data'));
            $s = intval($this->currentParams->get('qr-size'));
            $qrurl .= '&size=' . $s . 'x' . $s;
            $qrurl .= '&color=' . urlencode($this->currentParams->get('qr-fgcolor'));
            $qrurl .= '&bgcolor=' . urlencode($this->currentParams->get('qr-bgcolor'));
            $opts['qrCodeURL'] = $qrurl;
        }
        $this->renderTemplate('tools/qrcode', $opts);
    }

}
