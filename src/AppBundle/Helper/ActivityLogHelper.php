<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/5/2017
 * Time: 7:30 AM
 */

namespace AppBundle\Helper;

class ActivityLogHelper
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
        ActivityLogHelper::LOGIN,
        ActivityLogHelper::LOGOUT,
        ActivityLogHelper::VIEW_LESSON,
        ActivityLogHelper::CREATE_LESSON,
        ActivityLogHelper::EDIT_LESSON,
        ActivityLogHelper::SAVE_LESSON,
        ActivityLogHelper::DELETE_LESSON,
        ActivityLogHelper::VIEW_EXERCISE,
        ActivityLogHelper::CREATE_EXERCISE,
        ActivityLogHelper::EDIT_EXERCISE,
        ActivityLogHelper::SAVE_EXERCISE,
        ActivityLogHelper::DELETE_EXERCISE,
        ActivityLogHelper::SUBMIT_SOLUTION,
        ActivityLogHelper::SEARCH
    ];

    const EVENT_TYPE_DISPLAY_NAMES = [
        ActivityLogHelper::LOGIN => 'Log in',
        ActivityLogHelper::LOGOUT => 'Log out',
        ActivityLogHelper::VIEW_LESSON => 'View lesson',
        ActivityLogHelper::CREATE_LESSON => 'Create lesson',
        ActivityLogHelper::EDIT_LESSON => 'Edit lesson',
        ActivityLogHelper::SAVE_LESSON => 'Save lesson',
        ActivityLogHelper::DELETE_LESSON => 'Delete lesson',
        ActivityLogHelper::VIEW_EXERCISE => 'View exercise',
        ActivityLogHelper::CREATE_EXERCISE => 'Create exercise',
        ActivityLogHelper::EDIT_EXERCISE => 'Edit exercise',
        ActivityLogHelper::SAVE_EXERCISE => 'Save exercise',
        ActivityLogHelper::DELETE_EXERCISE => 'Delete exercise',
        ActivityLogHelper::SUBMIT_SOLUTION => 'Submit solution',
        ActivityLogHelper::SEARCH => 'Search'
    ];
}
