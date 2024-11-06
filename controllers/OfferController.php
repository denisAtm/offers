<?php

namespace app\controllers;

use Yii;
use app\models\Offer;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

class OfferController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    // Действие для отображения списка офферов
    public function actionIndex()
    {
        $query = Offer::find();

        // Фильтрация
        $offerName = Yii::$app->request->get('offer_name');
        $email = Yii::$app->request->get('representative_email');

        if ($offerName) {
            $query->andFilterWhere(['like', 'offer_name', $offerName]);
        }

        if ($email) {
            $query->andFilterWhere(['like', 'representative_email', $email]);
        }

        // Сортировка
        $sortParam = Yii::$app->request->get('sort', 'id');
        $sortAttribute = ltrim($sortParam, '-');
        $sortDirection = (strpos($sortParam, '-') === 0) ? SORT_DESC : SORT_ASC;

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy([$sortAttribute => $sortDirection]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionValidateForm($id = null)
    {
        $model = $id ? $this->findModel($id) : new Offer();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    public function actionCreate()
    {
        $model = new Offer();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Оффер успешно создан.');
                if (Yii::$app->request->isAjax) {
                    return 'success';
                } else {
                    return $this->redirect(['index']);
                }
            } else {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                }
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    // Действие для редактирования оффера
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Оффер успешно обновлен.');
                if (Yii::$app->request->isAjax) {
                    return 'success';
                } else {
                    return $this->redirect(['index']);
                }
            } else {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                }
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    // Действие для удаления оффера
    public function actionDelete($id)
    {
        if (Yii::$app->request->isAjax) {
            $this->findModel($id)->delete();
            return $this->asJson(['status' => 'success']);
        }

        throw new NotFoundHttpException('Страница не найдена.');
    }

    // Метод для поиска модели по ID
    protected function findModel($id)
    {
        if (($model = Offer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрошенная страница не найдена.');
    }
}
