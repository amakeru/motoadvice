<?php
header('Content-Type: application/json; charset=UTF-8');

$http_origin = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'];

if ($http_origin == "http://landing.back-light.ru")
{
    header("Access-Control-Allow-Origin: $http_origin");
}
header('Access-Control-Allow-Credentials: true');
define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(__FILE__))) . '/index.php';

$modx->getService('error','error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

$request = request();
$action = $request->input('action');
$out = ['success'=>false];

if(empty($action) || !$request->isAjax()) {
    die('Access denied');
}
else {
    $pdo = $modx->getService('pdoFetch');

    switch($action) {
        case 'mail':
            if($request->checkCsrfToken()) {
                $receivers = ['test@amake.ru'];
                $params = array(
                    // Обязательные параметры
                    'subject' => 'Пустая тема',
                    'content' => '',
                    // Необязательные параметры
                    'fromName' => $modx->getOption('site_name'),
                );
                $out['errors'] = [];
                if($data = $request->input()) {
                    $params['subject'] = $data['subject'] ?? $params['subject'];
                    $fields = [
                        'name' => [
                            'label' => 'Имя',
                            'error' => 'Укажите ваше имя'
                        ],
                        'phone' => [
                            'label' => 'Телефон',
                            'error' => 'Укажите ваш телефон'
                        ]
                    ];
                    $content = '';
                    foreach (['name', 'phone'] as $key) {
                        if(isset($data[$key])) {
                            if(!empty($data[$key])) {
                                if($key == 'phone' && strlen($data[$key]) != 18) {
                                    $out['errors'][$key] = 'Неверный формат номера';
                                }
                                else {
                                    $params['content'] .= '<p>' . $fields[$key]['label'] . ': <b>' . $data[$key] . '</b></p>';
                                    $newOrder[$key] = $data[$key];
                                }
                            }
                            else {
                                $out['errors'][$key] = $fields[$key]['error'];
                            }
                        }
                    }
                    if(count($out['errors'])) break;

                    if(isset($data['message']) && !empty($data['message'])) {
                        $params['content'] .= '<p>'.$data['message'].'</p>';
                    }

                    $modx->getService('mail', 'mail.modPHPMailer');
                    $modx->mail->set(modMail::MAIL_BODY, $params['content']);
                    $modx->mail->set(modMail::MAIL_FROM, 'admin@back-light.ru');
                    $modx->mail->set(modMail::MAIL_FROM_NAME, $modx->getOption('site_name'));
                    $modx->mail->set(modMail::MAIL_SUBJECT, $params['subject']);
                    foreach ($receivers as $receiver) {
                        $modx->mail->address('to', $receiver);
                    }
                    $modx->mail->setHTML(true);

                    //Attach multiple files one by one
                    if(isset($_FILES['files'])) {
                        for ($ct = 0; $ct < count($_FILES['files']['tmp_name']); $ct++) {
                            $uploadfile = tempnam('/var/www/newlight/tmp/', sha1($_FILES['files']['name'][$ct]));
                            $filename = $_FILES['files']['name'][$ct];
                            if (move_uploaded_file($_FILES['files']['tmp_name'][$ct], $uploadfile)) {
                                $modx->mail->attach($uploadfile, $filename);
                            } else {
                                $out['message'] .= 'Failed to move file to ' . $uploadfile;
                            }
                        }
                    }

                    if (!$modx->mail->send()) {
                        $modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the email: '.$modx->mail->mailer->ErrorInfo);
                        $out['success'] = false;
                        $out['message'] = 'Почта не отправилась.';
                    }
                    else {
                        $out['success'] = true;
                        $out['message'] = 'Почта успешно отправилась.';
                    }
                    $modx->mail->reset();
                }
            }
            break;

        default:
            $out['message'] = 'default action';
        break;
    }
}

@session_write_close();
exit(json_encode($out));
