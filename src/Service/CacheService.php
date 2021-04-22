<?php

namespace flexycms\FlexyCacheBundle\Service;

class CacheService
{
    const prodCacheDirectory = '/var/cache/prod/twig';

    /**
     * @return string
     */
    public function getSizeString($precision = 2): string
    {
        $size = $this->getFilesSize($_SERVER['DOCUMENT_ROOT'] . self::prodCacheDirectory);

        if ($size < 10000) $size .= " байт";
        else {
            $size = round($size / 1024, $precision);
            if ($size < 10000) $size .= " Кбайт";
            else {
                $size = round($size / 1024, $precision);
                $size .= " Мбайт";
            }
        }
        return $size;
    }


    public function liveExecuteCommand($cmd)
    {

        while (@ ob_end_flush()); // end all output buffers if any

        $proc = popen("$cmd 2>&1 ; echo Exit status : $?", 'r');

        $live_output     = "";
        $complete_output = "";

        while (!feof($proc))
        {
            $live_output     = fread($proc, 4096);
            $complete_output = $complete_output . $live_output;
            //echo "$live_output";
            @ flush();
        }

        pclose($proc);

        // get exit status
        preg_match('/[0-9]+$/', $complete_output, $matches);

        // return exit status and intended output
        if (is_array($matches) && isset($matches[0])) {
            return array (
                'exit_status'  => $matches[0],
                'output'       => str_replace("Exit status: " . $matches[0], '', $complete_output)
            );
        } else {
            return array (
                'exit_status'  => '0',
                'output'       => str_replace("Exit status: unknown", '', $complete_output)
            );
        }
    }




    public function clear(): void
    {
        $phpPath = PHP_BINDIR . '/php';
        $consolePath = $_SERVER['DOCUMENT_ROOT'] . '/bin/console';
        $result = $this->liveExecuteCommand("{$phpPath} {$consolePath} cache:clear");
    }

    private function getFilesSize($path)
    {
        $fileSize = 0;

        if (is_dir($path))
        {
            $dir = scandir($path);
            foreach ($dir as $file)
            {
                if (($file != '.') && ($file != '..'))
                    if (is_dir($path . '/' . $file))
                        $fileSize += $this->getFilesSize($path . '/' . $file);
                    else
                        $fileSize += filesize($path . '/' . $file);
            }
        }
        return $fileSize;
    }

    private function removeDir( $path )
    {
        // если путь существует и это папка
        if (file_exists($path) and is_dir($path))
        {
            // открываем папку
            $dir = opendir($path);
            while (false !== ($element = readdir($dir)))
            {
                // удаляем только содержимое папки
                if ($element != '.' and $element != '..')
                {
                    $tmp = $path . '/' . $element;
                    chmod($tmp, 0777);
                    // если элемент является папкой, то
                    // удаляем его используя нашу функцию removeDir
                    if (is_dir($tmp))
                    {
                        $this->removeDir($tmp);
                        // если элемент является файлом, то удаляем файл
                    } else {
                        unlink($tmp);
                    }
                }
            }
            // закрываем папку
            closedir($dir);
            // удаляем саму папку
            if (file_exists($path)) {
                rmdir($path);
            }
        }
    }



}