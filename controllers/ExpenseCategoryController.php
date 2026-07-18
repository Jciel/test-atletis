<?php

namespace app\controllers;

use app\models\forms\ExpenseCategoryForm;
use app\services\ExpenseCategoryService;
use Yii;

class ExpenseCategoryController extends ApiController
{
    public function __construct($id, $module, private ExpenseCategoryService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): array
    {
        return $this->service->findAll();
    }

    public function actionView(int $id): array
    {
        return $this->service->findById($id);
    }

    public function actionCreate(): array
    {
        $form = new ExpenseCategoryForm();
        $form->load(Yii::$app->request->bodyParams, '');
        $this->validateForm($form);
        return $this->service->create($form);
    }

    public function actionUpdate(int $id): array
    {
        $form = new ExpenseCategoryForm();

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
