<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/5/2017
 * Time: 7:30 AM
 */

namespace AppBundle\Helper;

use AppBundle\Entity\UserActivityLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Tests\AppBundle\Helper\UserHelperTest;

class UserActivityLogHelper
{
    const LOGIN = 'login';
    const LOGOUT = 'logout';
    const VIEW_LESSON = 'viewlesson';
    const CREATE_LESSON = 'createlesson';
    const EDIT_LESSON = 'editlesson';
    const SAVE_LESSON = 'savelesson';
    const DELETE_LESSON = 'deletelesson';
    const VIEW_EXERCISE = 'viewexercise';
    const CREATE_EXERCISE = 'createexercise';
    const EDIT_EXERCISE = 'editexercise';
    const SAVE_EXERCISE = 'saveexercise';
    const DELETE_EXERCISE = 'deleteexercise';
    const SUBMIT_SOLUTION = 'submitsolution';
    const SEARCH = 'search';

    const EVENT_TYPES = [
        UserActivityLogHelper::LOGIN,
        UserActivityLogHelper::LOGOUT,
        UserActivityLogHelper::VIEW_LESSON,
        UserActivityLogHelper::CREATE_LESSON,
        UserActivityLogHelper::EDIT_LESSON,
        UserActivityLogHelper::SAVE_LESSON,
        UserActivityLogHelper::DELETE_LESSON,
        UserActivityLogHelper::VIEW_EXERCISE,
        UserActivityLogHelper::CREATE_EXERCISE,
        UserActivityLogHelper::EDIT_EXERCISE,
        UserActivityLogHelper::SAVE_EXERCISE,
        UserActivityLogHelper::DELETE_EXERCISE,
        UserActivityLogHelper::SUBMIT_SOLUTION,
        UserActivityLogHelper::SEARCH
    ];

    const EVENT_TYPE_DISPLAY_NAMES = [
        UserActivityLogHelper::LOGIN => 'Log in',
        UserActivityLogHelper::LOGOUT => 'Log out',
        UserActivityLogHelper::VIEW_LESSON => 'View lesson',
        UserActivityLogHelper::CREATE_LESSON => 'Create lesson',
        UserActivityLogHelper::EDIT_LESSON => 'Edit lesson',
        UserActivityLogHelper::SAVE_LESSON => 'Save lesson',
        UserActivityLogHelper::DELETE_LESSON => 'Delete lesson',
        UserActivityLogHelper::VIEW_EXERCISE => 'View exercise',
        UserActivityLogHelper::CREATE_EXERCISE => 'Create exercise',
        UserActivityLogHelper::EDIT_EXERCISE => 'Edit exercise',
        UserActivityLogHelper::SAVE_EXERCISE => 'Save exercise',
        UserActivityLogHelper::DELETE_EXERCISE => 'Delete exercise',
        UserActivityLogHelper::SUBMIT_SOLUTION => 'Submit solution',
        UserActivityLogHelper::SEARCH => 'Search'
    ];

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /** @var UserHelper */
    protected $userHelper;

    /**
     * Constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserHelper $userHelper
     * @internal param EntityManagerInterface $em
     */
    public function __construct(
        EntityManagerInterface $entityManager, UserHelper $userHelper)
    {
        $this->entityManager = $entityManager;
        $this->userHelper = $userHelper;
    }

    /**
     * @param string $eventType
     * @param Request $request
     * @param array $extra
     * @throws \Exception
     */
    public function logEvent(string $eventType, Request $request, array $extra = null) {
        if ( ! in_array($eventType, UserActivityLogHelper::EVENT_TYPES) ) {
            throw new \Exception('User activity log: Unknown event type: ' . $eventType);
        }
        $logEntry = new UserActivityLog();
        $logEntry
            ->setEventType($eventType)
            ->setIp($request->getClientIp())
            ->setUrl($request->getPathInfo());
        if ( ! is_null($extra) ) {
            $logEntry->setExtra($extra);
        }
        $user = $this->userHelper->getLoggedInUser();
        if ( ! is_null($user) ) {
            $logEntry->setUser($user);
        }
        $this->entityManager->persist($logEntry);
        $this->entityManager->flush();    }
}
