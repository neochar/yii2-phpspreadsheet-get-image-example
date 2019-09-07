<?php

namespace app\commands;

use library\CellFilter;
use library\Helper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Class ScriptController
 * @package app\commands
 */
class ScriptController extends Controller
{
    /**
     * @param string $filename
     * @return int Exit code
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function actionIndex($filename = 'template.xlsx')
    {

        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $i = 0;
        foreach ($spreadsheet->getActiveSheet()->getDrawingCollection() as $image) {
            $coordinates = $image->getCoordinates();
            if ($image instanceof MemoryDrawing) {
                ob_start();
                call_user_func(
                    $image->getRenderingFunction(),
                    $image->getImageResource()
                );
                $imageContents = ob_get_contents();
                ob_end_clean();
                switch ($image->getMimeType()) {
                    case MemoryDrawing::MIMETYPE_PNG :
                        $extension = 'png';
                        break;
                    case MemoryDrawing::MIMETYPE_GIF:
                        $extension = 'gif';
                        break;
                    case MemoryDrawing::MIMETYPE_JPEG :
                        $extension = 'jpg';
                        break;
                }
            } else {
                $zipReader = fopen($image->getPath(), 'r');
                $imageContents = '';
                while (!feof($zipReader)) {
                    $imageContents .= fread($zipReader, 1024);
                }
                fclose($zipReader);
                $extension = $image->getExtension();
            }
            $myFileName = '00_Image_' . ++$i . '.' . $extension;
            file_put_contents(
                Yii::getAlias("@images/$myFileName"),
                $imageContents
            );
        }


        return ExitCode::OK;
    }
}
