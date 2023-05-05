<?php
namespace component\Locale;

use Phalcon\Di\Injectable;
use Phalcon\Translate\Adapter\NativeArray;
use Phalcon\Translate\InterpolatorFactory;
use Phalcon\Translate\TranslateFactory;

session_start();
class Locale extends Injectable
{
    /**
     * @return NativeArray
     */
    public function getTranslator(): NativeArray
    {
        $language = $_SESSION['language'];
        $messages = [];
        if(isset($_SESSION['language'])) {
            $translationFile = APP_PATH.'/messages/' . $language . '.php';
        }
        if (true !== file_exists($translationFile)) {
            $translationFile = APP_PATH.'/messages/en-GB.php';
        }
        require $translationFile;

        $interpolator = new InterpolatorFactory();
        $factory      = new TranslateFactory($interpolator);
        
        return $factory->newInstance(
            'array',
            [
                'content' => $messages,
            ]
        );
    }
}