<?php

namespace app\services;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

abstract class BaseService
{
    protected function saveOrFail(ActiveRecord $model): void
    {
        if ($model->save()) {
            return;
        }

        throw new BadRequestHttpException(
            implode("\n", $model->getFirstErrors())
        );
    }

    protected function findOrFail(string $modelClass, int $id): ActiveRecord
    {
        $model = $modelClass::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException('Registro não encontrado.');
        }

        return $model;
    }


    protected function findOneOrFail(ActiveQuery $query): ActiveRecord
    {
        $model = $query->one();

        if ($model === null) {
            throw new NotFoundHttpException('Registro não encontrado.');
        }

        return $model;
    }

    protected function deleteOrFail(ActiveRecord $model): void
    {
        if ($model->delete() === false) {
            throw new BadRequestHttpException('Não foi possível excluir o registro.');
        }
    }
}
