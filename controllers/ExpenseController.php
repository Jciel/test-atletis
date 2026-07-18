<?php

namespace app\controllers;

use app\models\forms\ExpenseSearchForm;
use Yii;
use app\models\forms\ExpenseForm;
use app\services\ExpenseService;

class ExpenseController extends ApiController
{

    public function __construct($id, $module, private ExpenseService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): array
    {
        $form = new ExpenseSearchForm();

        $form->load(Yii::$app->request->queryParams, '');

        $this->validateForm($form);

        return $this->service->findAll($form);
    }

    public function actionView(int $id): array
    {
        return $this->service->findById($id);
    }

    public function actionCreate(): array
    {
        $form = new ExpenseForm();
        $this->validateForm($form);
        Yii::$app->response->statusCode = 201;

        return $this->service->create($form);
    }

    public function actionUpdate(int $id): array
    {
        $form = new ExpenseForm();
        $this->validateForm($form);

        return $this->service->update($id, $form);
    }

    public function actionDelete(int $id): yii\web\Response
    {
        $this->service->delete($id);

        Yii::$app->response->statusCode = 204;

        return Yii::$app->response;
    }




}
