<?php

namespace frontend\controllers;

use common\models\Project;
use common\models\User;
use common\services\ProjectService;
use common\services\TaskService;
use common\utils\ResponseUtil;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Site controller
 */
class DefaultController extends BaseController
{
    public $layout = 'main-member';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['logout', 'signup', 'index', 'dynamic'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow'   => true,
                        'roles'   => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'index', 'dynamic'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error'   => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class'           => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * 首页
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        $projects = TaskService::factory()->getProjectStatByCompanyID($user->fdCompanyID);
        return $this->render('index', [
            'projects' => $projects
        ]);
    }

    /**
     * 最新动态
     * TODO 需要实时更新最新动态
     */
    public function actionDynamic()
    {
        return $this->render('dynamic');
    }

    /**
     * 弹窗提示
     * @since 2018-01-15
     */
    public function actionShowBox()
    {
        $data = Yii::$app->session->get('showbox');
        $data = $data ? json_decode($data, true) : array();
        Yii::$app->session->set('showbox', null);
        ResponseUtil::jsonCORS($data);
    }

}
