<?php
/**
 * Helps with tasks about keywords.
 * User: kieran
 * Date: 12/1/2017
 * Time: 11:44 AM
 */

namespace AppBundle\Helper;


use Doctrine\ORM\EntityManagerInterface;

class KeywordHelper
{
    /** What the keyword title field is called in the database and on forms. */
    const KEYWORD_TITLE_FIELD_NAME = 'title';
    /** What the keyword notes field is called in the database and on forms. */
    const KEYWORD_NOTES_FIELD_NAME = 'notes';

    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    /** @var FormErrorMessageHelper $formErrorMessageHelper */
    protected $formErrorMessageHelper;

    /**
     * Constructor. Load some dependencies.
     *
     * @param EntityManagerInterface $entityManager
     * @param FormErrorMessageHelper $formErrorMessageHelper
     */
    public function __construct(EntityManagerInterface $entityManager, FormErrorMessageHelper $formErrorMessageHelper)
    {
        //Store service references.
        $this->entityManager = $entityManager;
        $this->formErrorMessageHelper = $formErrorMessageHelper;
    }

    /**
     * Check whether a string has the format of a keyword title.
     * Add messages to error message helper if needed.
     * @param string $toCheck The value to check.
     * @return bool True if it could be keyword title.
     */
    public function validateTitleFormat(string $toCheck) {
        $mt = is_null($toCheck) || $toCheck === '';
        if ( $mt ) {
            $this->formErrorMessageHelper->recordFieldErrorMessage(
                self::KEYWORD_TITLE_FIELD_NAME,
                'Sorry, the keyword cannot be blank.'
            );
        }
        return ! $mt;
    }

    /**
     * Check whether a keyword with a given title already exists.
     * Add messages to error message helper if needed.
     * @param string $toCheck The value to check.
     * @return bool True if the title is in use.
     */
    public function checkKeywordInUse(string $toCheck) {
        //Check if the title is already being used by an existing keyword.
        $keyword = $this->entityManager->getRepository('AppBundle:Keyword')
            ->findOneBy([self::KEYWORD_TITLE_FIELD_NAME => $toCheck]);
        $inUse = ! is_null($keyword);
        if ( $inUse ) {
            $this->formErrorMessageHelper->recordFieldErrorMessage(
                self::KEYWORD_TITLE_FIELD_NAME,
                'Sorry, that keyword already exists.'
            );
        }
        return $inUse;
    }

}
