<?php

namespace App\Greenter\Utils;


use Greenter\Data\DocumentGeneratorInterface;
use Greenter\Data\GeneratorFactory;
//use Greenter\Data\SharedStore;
use Greenter\Model\DocumentInterface;
use Greenter\Model\Response\CdrResponse;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Report\HtmlReport;
use Greenter\Report\PdfReport;
use Greenter\Report\Resolver\DefaultTemplateResolver;
use Greenter\Report\XmlUtils;
use Greenter\See;
use App\Greenter\data\SharedStore;
use App\Mantenimiento\Sedes\Sede;
use Exception;

final class Util
{
    /**
     * @var Util
     */
    private static $current;
    /**
     * @var SharedStore
     */
    public $shared;

    private function __construct()
    {
        $this->shared = new SharedStore();
    }

    public static function getInstance(): Util
    {
        if (!self::$current instanceof self) {
            self::$current = new self();
        }

        return self::$current;
    }

    public function getSee(?string $endpoint,$greenter_config)
    {
        $see = new See();
        $see->setService($endpoint);
        //$see->setCodeProvider(new XmlErrorCodeProvider());
        //$certificate = file_get_contents(__DIR__ . '/../certificate/certificate_test.pem');
        //$certificate = file_get_contents(__DIR__ . '/../certificate/certificate_merris.pem');

        //====== OBTENIENDO RUTA DEL CERTIFICADO =======
        $certificadoPath    =   storage_path('app/public/' . $greenter_config->ruta_certificado);

        if(!file_exists($certificadoPath)){
            throw new Exception('No existe el certificado,debe registrar uno en Mantenimiento/Empresas');
        }

        $certificate    =    file_get_contents($certificadoPath);

        if ($certificate === false) {
            throw new Exception('No se pudo cargar el certificado');
        }

        $see->setCertificate($certificate);
        /**
         * Clave SOL
         * Ruc     = 20000000001
         * Usuario = MODDATOS
         * Clave   = moddatos
         */
        //$see->setClaveSOL('20000000001', 'MODDATOS', 'moddatos');
        //$see->setClaveSOL('20611904020', 'SISCOMFA', 'Merry321');

        //========= ESTABLECIENDO CREDENCIALES =======
        $sol_user   =   $greenter_config->sol_user;
        $sol_pass   =   $greenter_config->sol_pass;
        $ruc        =   $greenter_config->ruc;

        $see->setClaveSOL($ruc, $sol_user, $sol_pass);

        $see->setCachePath(__DIR__ . '/../cache');

        return $see;
    }

    public function getSeeApi($greenter_config)
    {
        $ruc    =   $greenter_config->ruc;
        $api    =   null;

        if($greenter_config->modo === "BETA"){
            $api = new \Greenter\Api([
                'auth' => 'https://gre-test.nubefact.com/v1',
                'cpe' => 'https://gre-test.nubefact.com/v1',
            ]);

            //$ruc    =   "20161515648";
        }

        if($greenter_config->modo === "PRODUCCION"){
            $api = new \Greenter\Api([
                'auth' => 'https://api-seguridad.sunat.gob.pe/v1',
                'cpe' => 'https://api-cpe.sunat.gob.pe/v1',
            ]);
        }

        $certificadoPath    =   storage_path('app/public/' . $greenter_config->ruta_certificado);

        if(!file_exists($certificadoPath)){
            throw new Exception('No existe el certificado,debe registrar uno en Mantenimiento/Empresas');
        }

        $certificate    =    file_get_contents($certificadoPath);

        if ($certificate === false) {
            throw new Exception('No se pudo cargar el certificado');
        }

        //$certificate = file_get_contents(__DIR__ . '/../certificate/certificate_test.pem');

        // if ($certificate === false) {
        //     throw new Exception('No se pudo cargar el certificado');
        // }


        return $api->setBuilderOptions([
                'strict_variables' => true,
                'optimizations' => 0,
                'debug' => true,
                'cache' => false,
            ])
            ->setApiCredentials($greenter_config->id_api_guia_remision, $greenter_config->clave_api_guia_remision)
            ->setClaveSOL($ruc, $greenter_config->sol_user, $greenter_config->sol_pass)
            ->setCertificate($certificate);


            //  ->setApiCredentials('test-85e5b0ae-255c-4891-a595-0b98c65c9854', 'test-Hty/M6QshYvPgItX2P0+Kw==')
            //  ->setClaveSOL('20161515648', 'MODDATOS', 'MODDATOS')
            // ->setApiCredentials('9e8eaf55-cf1d-4bf0-9837-0c3d897c08d5', '3xSHGqcy5mglRIJzxx6eZw==')
            // ->setClaveSOL('20611904020', 'SISCOMFA', 'Merry321')
    }

    public function getGRECompany(): \Greenter\Model\Company\Company
    {
        $sede   =   Sede::find(1);

        return (new \Greenter\Model\Company\Company())
            ->setRuc($sede->ruc)
            ->setRazonSocial($sede->razon_social);
        /*
        return (new \Greenter\Model\Company\Company())
            ->setRuc('20161515648')
            ->setRazonSocial('GREENTER S.A.C.');
        */
    }

    public function showResponse(DocumentInterface $document, CdrResponse $cdr): void
    {
        $filename = $document->getName();

        require __DIR__.'/../views/response.php';
    }

    public function getErrorResponse(\Greenter\Model\Response\Error $error): string
    {
        $result = <<<HTML
        <h2 class="text-danger">Error:</h2><br>
        <b>Código:</b>{$error->getCode()}<br>
        <b>Descripción:</b>{$error->getMessage()}<br>
HTML;

        return $result;
    }

    public function writeXml(?DocumentInterface $document, ?string $xml,?string $tipo_comprobante,?string $document_name): void
    {
        $doc_name   = null;
        if($document){
            $doc_name   =   $document->getName();
        }else{
            $doc_name   =   $document_name;
        }

        // $this->writeFile($document->getName().'.xml', $xml,"xml",$tipo_comprobante);
        $this->writeFile($doc_name.'.xml', $xml,"xml",$tipo_comprobante);
    }

    public function writeCdr(?DocumentInterface $document, ?string $zip,?string $tipo_comprobante,?string $document_name): void
    {
        $doc_name   = null;
        if($document){
            $doc_name   =   $document->getName();
        }else{
            $doc_name   =   $document_name;
        }

        // $this->writeFile($document->getName().'.zip', $zip,"zip",$tipo_comprobante);
        $this->writeFile($doc_name.'.zip', $zip,"zip",$tipo_comprobante);

    }

    public function writeFile(?string $filename, ?string $content,?string $typeFile,?string $tipo_comprobante): void
    {
        if (getenv('GREENTER_NO_FILES')) {
            return;
        }

        if($typeFile    ==  "zip"){
            if($tipo_comprobante == 'RESUMEN'){
                //$fileDir = __DIR__.'/../files/resumenes_cdr';
                $fileDir    =   public_path('storage/greenter/resumenes/cdr');
            }
            if($tipo_comprobante == 'GUIA REMISION'){
                $fileDir    =   public_path('storage/greenter/guías_remisión/cdr');
            }
            if($tipo_comprobante == 127){  //======== FACTURA ====
                $fileDir    =   public_path('storage/greenter/facturas/cdr');
            }
            if($tipo_comprobante == 128){  //======== BOLETA ====
                $fileDir    =   public_path('storage/greenter/boletas/cdr');
            }
            if($tipo_comprobante == '07-03'){
                $fileDir    =   public_path('storage/greenter/notas_credito_boletas/cdr');
            }
            if($tipo_comprobante == '07-01'){
                $fileDir    =   public_path('storage/greenter/notas_credito_facturas/cdr');
            }
        }

        if($typeFile    ==  "xml"){

            if($tipo_comprobante == 'RESUMEN'){
                //$fileDir = __DIR__.'/../files/resumenes_xml';
                $fileDir    =   public_path('storage/greenter/resumenes/xml');
            }
            if($tipo_comprobante == 'GUIA REMISION'){
                $fileDir    =   public_path('storage/greenter/guías_remisión/xml');
            }
            if($tipo_comprobante == 127){   //======== FACTURA =====
                $fileDir    =   public_path('storage/greenter/facturas/xml');
            }
            if($tipo_comprobante == 128){   //======== BOLETA =====
                $fileDir    =   public_path('storage/greenter/boletas/xml');
            }
            if($tipo_comprobante == '07-03'){
                $fileDir    =   public_path('storage/greenter/notas_credito_boletas/xml');
            }
            if($tipo_comprobante == '07-01'){
                $fileDir    =   public_path('storage/greenter/notas_credito_facturas/xml');
            }
        }

        if (!file_exists($fileDir)) {
         
            mkdir($fileDir, 0777, true);
        }

        if(!file_exists($fileDir.DIRECTORY_SEPARATOR.$filename)){
            file_put_contents($fileDir.DIRECTORY_SEPARATOR.$filename, $content);

        }
    }

    public function getPdf(DocumentInterface $document): ?string
    {
        $html = new HtmlReport('', [
            'cache' => __DIR__ . '/../cache',
            'strict_variables' => true,
        ]);
        $resolver = new DefaultTemplateResolver();
        $template = $resolver->getTemplate($document);
        $html->setTemplate($template);

        $render = new PdfReport($html);
        $render->setOptions( [
            'no-outline',
            'print-media-type',
            'viewport-size' => '1280x1024',
            'page-width' => '21cm',
            'page-height' => '29.7cm',
            'footer-html' => __DIR__.'/../resources/footer.html',
        ]);
        $binPath = self::getPathBin();
        if (file_exists($binPath)) {
            $render->setBinPath($binPath);
        }
        $hash = $this->getHash($document);
        $params = self::getParametersPdf();
        $params['system']['hash'] = $hash;
        $params['user']['footer'] = '<div>consulte en <a href="https://github.com/giansalex/sufel">sufel.com</a></div>';

        $pdf = $render->render($document, $params);

        if ($pdf === null) {
            $error = $render->getExporter()->getError();
            echo 'Error: '.$error;
            exit();
        }

        // Write html
        $this->writeFile($document->getName().'.html', $render->getHtml());

        return $pdf;
    }

    public function getGenerator(string $type): ?DocumentGeneratorInterface
    {
        $factory = new GeneratorFactory();
        $factory->shared = $this->shared;

        return $factory->create($type);
    }

    /**
     * @param SaleDetail $item
     * @param int $count
     * @return array<SaleDetail>
     */
    public function generator(SaleDetail $item, int $count): array
    {
        $items = [];

        for ($i = 0; $i < $count; $i++) {
            $items[] = $item;
        }

        return $items;
    }

    public function showPdf(?string $content, ?string $filename): void
    {
        $this->writeFile($filename, $content);
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . strlen($content));

        echo $content;
    }

    public static function getPathBin(): string
    {
        $path = __DIR__.'/../vendor/bin/wkhtmltopdf';
        if (self::isWindows()) {
            $path .= '.exe';
        }

        return $path;
    }

    public static function isWindows(): bool
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    private function getHash(DocumentInterface $document): ?string
    {
        $see = $this->getSee('');
        $xml = $see->getXmlSigned($document);

        return (new XmlUtils())->getHashSign($xml);
    }

    /**
     * @return array<string, array<string, array<int, array<string, string>>|bool|string>>
     */
    private static function getParametersPdf(): array
    {
        $logo = file_get_contents(__DIR__.'/../resources/logo.png');

        return [
            'system' => [
                'logo' => $logo,
                'hash' => ''
            ],
            'user' => [
                'resolucion' => '212321',
                'header' => 'Telf: <b>(056) 123375</b>',
                'extras' => [
                    ['name' => 'FORMA DE PAGO', 'value' => 'Contado'],
                    ['name' => 'VENDEDOR', 'value' => 'GITHUB SELLER'],
                ],
            ]
        ];
    }
}
