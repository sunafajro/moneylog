<?php

namespace backend\controllers;

use common\models\forms\UserForm;
use common\rbac\Permission;
use Yii;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ServerErrorHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index' ,'view'],
                        'roles' => [Permission::PERMISSION_USER_VIEW_ANY],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => [Permission::PERMISSION_USER_CREATE_NEW],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => [Permission::PERMISSION_USER_UPDATE_ANY],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => [Permission::PERMISSION_USER_DELETE_ANY],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find()->byActive(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView(int $id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return mixed
     * @throws ServerErrorHttpException
     */
    public function actionCreate()
    {
        $model = new UserForm();

        try {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } catch (\Exception $e) {
            throw new ServerErrorHttpException($e->getMessage());
        }


        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionUpdate(int $id)
    {
        $user = $this->findModel($id);
        $model = UserForm::loadFromModel($user);
        try {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } catch (\Exception $e) {
            throw new ServerErrorHttpException($e->getMessage());
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionDelete(int $id)
    {
        $user = $this->findModel($id);
        try {
            $user->delete();
        } catch (\Throwable $e) {
            throw new ServerErrorHttpException($e->getMessage());
        }

        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return User
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): User
    {
        if (($model = User::find()->byId($id)->byActive()->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
