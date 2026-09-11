<?php

namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public $layout = 'front';
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'layout' => 'blank',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */

    
    public function actionListascontrini()
    {
        return $this->redirect(['scontrino/index']);
    }

    public function actionCaricascontrino()
    {
        return $this->redirect(['scontrino/create']);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/dashboard']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/dashboard']);
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Grazie per averci contattato. Risponderemo nel più breve tempo possibile.');
            } else {
                Yii::$app->session->setFlash('error', 'Ops! Abbiamo riscontrato un errore durante l\'invio del tuo messaggio.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Mostra la pagina Chi Siamo.
     *
     * @return mixed
     */
    public function actionChisiamo()
    {
        return $this->render('chisiamo');
    }

    /**
     * Mostra la pagina Regolamento.
     *
     * @return mixed
    */
    public function actionRegolamento()
    {
        return $this->render('regolamento');
    }

    /**
     * Mostra la pagina Regolamento.
     *
     * @return mixed
    */
    public function actionServizi()
    {
        return $this->render('servizi');
    }

    /**
     * Mostra la pagina Premi.
     *
     * @return mixed
    */
    public function actionPremi()
    {
        return $this->render('premi');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup($b2b = 0)
    {
        $model = new SignupForm();
        $model->b2b = $b2b;
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Registrazione avvenuta con successo. Controlla la tua email per attivare l\'account.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'La mail per il reset della password è stata inviata correttamente all\'indirizzo specificato.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Purtroppo non è possibile eseguire il reset della password per l\'account specificato.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Fantastico! Password variata correttamente.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if (($user = $model->verifyEmail()) && Yii::$app->user->login($user)) {
            // assegna il ruolo di utente registrato solo dopo la verifica dell'email
            $auth = Yii::$app->authManager;
            $user_role = $auth->createRole('simpleuser');
            $auth->assign($user_role, $user->id);
            Yii::$app->session->setFlash('success', 'Congratulazioni! La tua email è stata validata con successo.');
            return $this->redirect(['/dashboard']);
        }

        Yii::$app->session->setFlash('error', 'Siamo spiacenti. Non siamo riusciti a validare il token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'La mail per il reset della password è stata inviata correttamente all\'indirizzo specificato.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Purtroppo non è possibile eseguire il reset della password per l\'account specificato.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }
}
