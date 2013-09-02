<?php
class Ajax extends CComponent {
	const AJAX_ERROR = 'error';
	const AJAX_NOTICE = 'notice';
	const AJAX_SUCCESS = 'success';
	const AJAX_WARNING = 'warning';

	/**
	 * Send ajax answers
	 *
	 * @param string      $status
	 * @param string      $message
	 * @param array       $data
	 */
	static function send ( $status, $message, $data = array() ) {
		if ( Yii::app()->getRequest()->getIsAjaxRequest() ) {
			if ( $status == self::AJAX_ERROR || $status == self::AJAX_WARNING ) {
				$header = 'HTTP/1.1 500 Internal Server Error';
			}
			else {
				$header = 'HTTP/1.1 200 OK';
			}

			if ( !headers_sent() ) {
				header($header);
			}

			echo CJSON::encode(array(
			                        'status'  => $status,
			                        'message' => $message,
			                        'data'    => $data,
			                   ));

			Yii::app()->end();
		}

		if ( !empty($data['redirectUrl']) ) {
			Yii::app()->getController()->redirect($data['redirectUrl']);
		}
	}

	/**
	 * renderPartial if ajax request or render if not
	 *
	 * @param       $view
	 * @param array $data
	 */
	static function renderAjax ( $view, $data, $return = false, $processOutput = false, $asPlainText = false ) {
		if ( Yii::app()->getRequest()->getIsAjaxRequest() ) {
			if ( $asPlainText ) {
				Yii::app()->getController()->renderPartial($view, $data, $return, $processOutput);
			}
			else {
				Ajax::send(Ajax::AJAX_SUCCESS,
					'',
					array(
					     'form' => Yii::app()->getController()->renderPartial($view,
						     $data,
						     $return,
						     $processOutput)
					));
			}
		}
		else {
			Yii::app()->getController()->render($view, $data, $return);
		}
	}

	static function saveModel ( $model, $successText = '', $errorText = '' ) {
		if ( !$successText ) {
			$successText = Yii::t('main', 'Запись сохранена успешно');
		}
		if ( !$errorText ) {
			$errorText = Yii::t('main', 'Возникли ошибки при сохранении данных: {errors}');
		}
		if ( $model->save() ) {
			if ( Yii::app()->getRequest()->getIsAjaxRequest() ) {
				Ajax::send(Ajax::AJAX_SUCCESS, $successText);
			}
			else {
				Yii::app()->user->setFlash('flashMessage', $successText);
			}
			return true;
		}
		else {
			$errors = '';
			foreach ( $model->getErrors() AS $key => $text ) {
				$errors .= ($errors ? '<br />' : '') . implode(', ', $text);
			}

			if ( Yii::app()->getRequest()->getIsAjaxRequest() ) {
				Ajax::send(Ajax::AJAX_ERROR, str_replace('{errors}', $errors, $errorText));
			}
			else {
				Yii::app()->user->setFlash('flashMessage', str_replace('{errors}', $errors, $errorText));
			}
			return false;
		}
	}
}