<?php

namespace frontend\controllers;

use Yii;
use common\models\Post;
use common\models\PostSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\filters\AccessControl;

/**
 * PostController implements the CRUD actions for Post model.
 */
class PostController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'update', 'create', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index' , 'view'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['update', 'create', 'delete', 'index' , 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Post models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    // enhanced functions
    public function actionMyIndex() 
    {
        $post = Post::find()->all();
            // print_r($post);
            // die();
        return $this->render('myindex', ['posts' => $post]);
    }

    /**
     * Displays a single Post model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }



    // enhanced functions
    public function actionMyView($id)
    {
        return $this->render('myview', [
            'model' => $this->findModel($id),
        ]);
    }


    /**
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Post();

        if ($model->load(Yii::$app->request->post()) ) {


            $model->post_uploader = Yii::$app->user->identity->id;
            $model->post_timestamp = time();
            $model->post_update_timestamp = 0;

            $image = UploadedFile::getInstance($model, 'post_image');

             if(empty($image)) {
                $model->save();     
                return $this->redirect(['my-view', 'id' => $model->post_id]);
            }

            $model->post_image = $image->baseName.'.'.$image->extension;
           
            if($model->save()) {

                $image->saveAs('uploads/'.$model->post_image);
                return $this->redirect(['my-view', 'id' => $model->post_id]);
            
            }
            else {
                echo $image->baseName.'.'.$image->extension . " - image can not upload"; die();
            }

        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Post model.
     * If update is successful, the browser will be redirected to the 'my-view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->post_uploader == Yii::$app->user->identity->id) {

// die("fuck yeh kya");

            $model->post_uploader = Yii::$app->user->identity->id;
            $model->post_timestamp = $model->post_timestamp;
            $model->post_update_timestamp = time();



            $image = UploadedFile::getInstance($model, 'post_image');

            if(empty($image)) {
                $model->save();     
                return $this->redirect(['my-view', 'id' => $model->post_id]);
            }


            $model->post_image = $image->baseName.'.'.$image->extension;
           
            if($model->save()) {

                $image->saveAs('uploads/'.$model->post_image);
                return $this->redirect(['my-view', 'id' => $model->post_id]);
            
            }
            else {
                echo $image->baseName.'.'.$image->extension . " - image can not upload"; die();
            }


        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Post model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        // return $this->redirect(['index']);
        return $this->goHome();
    }

    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Post::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
