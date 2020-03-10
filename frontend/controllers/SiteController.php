<?php
namespace frontend\controllers;

use frontend\helpers\ParseHelper;
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

ini_set('max_execution_time', 170);
ini_set('memory_limit', '256M');
set_time_limit(0);
//phpinfo();
//exit('exit');
/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
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
                'class' => VerbFilter::className(),
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
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @return string
     */
    public function actionParse()
    {
        $response = ParseHelper::getRequestToUrl('rbk.ru');
        $htmlDom = ParseHelper::dom($response);
        $shortResultArray = [];

        $links = $htmlDom->query('//div[@class="js-news-feed-list"]/a[@class="news-feed__item js-news-feed-item js-yandex-counter"]')->length;

        for ($i = 0; $i <= $links - 1; $i++) {
            $item = $htmlDom->query('//div[@class="js-news-feed-list"]/a[@class="news-feed__item js-news-feed-item js-yandex-counter"]')->item($i)->nodeValue ?? null;
            $href = $htmlDom->query('//div[@class="js-news-feed-list"]/a[@class="news-feed__item js-news-feed-item js-yandex-counter"]/@href')->item($i)->nodeValue ?? null;
            $title = $htmlDom->query('//div[@class="js-news-feed-list"]/a[@class="news-feed__item js-news-feed-item js-yandex-counter"]/span[contains(@class, "news-feed__item__title")]/text()')->item($i)->nodeValue ?? null;
            $dateBlock = $htmlDom->query('//div[@class="js-news-feed-list"]/a[@class="news-feed__item js-news-feed-item js-yandex-counter"]/span[@class="news-feed__item__date"]/span[@class="news-feed__item__date-text"]/text()')->item($i)->nodeValue ?? null;
            $additionalData = explode(',', trim($dateBlock));
            $category = $additionalData[0] ?? 'нет данных';
            $time = $additionalData[1] ?? 'нет данных';

            $shortResultArray[$i] = [
                'item' => trim($item),
                'href' => trim($href),
                'title' => trim($title),
                'category' => trim($category),
                'time' => trim($time)
            ];
        }

        $fullDataResult = [];
        foreach($shortResultArray as $k => $item) {
            $response = ParseHelper::getRequestToUrl($item['href']);
            $htmlDom = ParseHelper::dom($response);

            $title = $htmlDom->query('//h1[@class="js-slide-title"]/text()')->item(0)->nodeValue ?? null;
            $image = $htmlDom->query('//div[@class="article__main-image__wrap"]/img/@src')->item(0)->nodeValue ?? null;
            $subTitle = $htmlDom->query('//div[@class="article__subtitle"]/text()')->item(0)->nodeValue ?? null;
            $fullTextsLength = $htmlDom->query('//div[@class="article__text article__text_free"]/p')->length ?? null;

            $p = '';
            for($i = 0; $i < $fullTextsLength - 1; $i++) {
                $p .= $htmlDom->query('//div[@class="article__text article__text_free"]/p/text()')->item($i)->nodeValue ?? null;
            }

            $fullDataResult[$k] = [
                'title' => trim($title),
                'image' => trim($image),
                'subTitle' => trim($subTitle),
                'fullText' => trim($p),
            ];

        }

        return $this->render('parse', [
            'fullDataResult' => $fullDataResult
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
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
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
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
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
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
            Yii::$app->session->setFlash('success', 'New password saved.');

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
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
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
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }
}
