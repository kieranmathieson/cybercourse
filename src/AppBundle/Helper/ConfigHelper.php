<?php
/**
 * Helper for application config.
 * User: kieran
 * Date: 12/15/2017
 * Time: 11:44 AM
 */

namespace AppBundle\Helper;

use Exception;
use Symfony\Component\Yaml\Yaml;

class ConfigHelper
{
    const COURSE_CONFIG_FILE_NAME = 'skill_course.yml';

    /** @var  FileHelper $fileHelper */
    protected $fileHelper;

    /**
     * ConfigHelper constructor.
     * @param FileHelper $fileHelper
     */
    public function __construct(FileHelper $fileHelper)
    {
        $this->fileHelper = $fileHelper;
    }

    /**
     * Get the course configuration.
     * @return array Configuration.
     * @throws Exception
     */
    public function getCourseConfig()
    {
        $path = $this->fileHelper->normalizePath(
           $this->fileHelper->getAppRootFilePath() . '/config/' . self::COURSE_CONFIG_FILE_NAME
        );
        try {
            $config = Yaml::parseFile($path);
        } catch (Exception $e) {
            //Todo: something different here. Admin needs more guidance if there is a
            //config error.
            throw $e;
        }
        return $config;
    }
}