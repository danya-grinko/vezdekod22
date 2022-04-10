<?php

    require_once './../.lib/vk/autoload.php';
    require_once 'function.php';
    require_once 'config.php';

    define ("VK_TOKEN", $bot_vk ['vk_token']);
    define ("GROUP_ID", $bot_vk ['group_id']);
    define ("SECRET_KEY", $bot_vk ['secret_key']);
    define ("CONFIRMATION_CODE", $bot_vk ['confirmation_code']);
    define ("API_VK_VERSION", $bot_vk ['api_vk_version']);

    define ("BD_HOST", $bd ['host']);
    define ("BD_DATABASE", $bd ['database']);
    define ("BD_USER", $bd ['user']);
    define ("BD_PASSWORD", $bd ['password']);


    use VK\Client\Enums\VKLanguage;
    use VK\Client\VKApiClient;

    function myLog($str) {
        file_put_contents("php://stdout", "$str\n");
    }

        //Кнопки :
    //Красная
    const COLOR_NEGATIVE = 'negative';
    //Зеленая
    const COLOR_POSITIVE = 'positive';
    //Белая
    const COLOR_DEFAULT = 'default';
    //Синяя
    const COLOR_PRIMARY = 'primary';


    $json = file_get_contents('php://input');
    myLog($json);

    $data = json_decode($json, true);
    $type = $data['type'] ?? '' ;
    $object = $data ['object'] ?? [];
    $message_array_json = $object ['message'] ?? [] ;
    $user_id = $message_array_json ['from_id'] ?? '';

    $group_id = $data ['group_id'] ?? 0;
    $event_id = $data ['event_id'] ?? '';
    $secret_key = $data ['secret'] ?? '';


    $vk = new VKApiClient(API_VK_VERSION, VKLanguage::RUSSIAN);

    if ($type == 'confirmation' && $secret_key == SECRET_KEY) {
        exit (CONFIRMATION_CODE);
    } elseif ($type == 'confirmation') {
        exit ('error:confirmation');
    }

    $user = user ($user_id);


    if ($type == "message_new") {


        $vk = new VKApiClient(API_VK_VERSION, VKLanguage::RUSSIAN);
        $response = $vk->users()->get(VK_TOKEN, array(
            'user_ids' => $user_id,
            'fields' => array('first_name', 'last_name', 'city', 'bdate'),
        ));
        $first_name = $response[0]['first_name'] ?? '';
        $last_name = $response[0]['last_name'] ?? '';
        $city = $response[0]['city']['title'] ?? 'неизвестный';
        $bdate = $response[0]['bdate'];

        $age = floor( ( time() - strtotime($bdate) ) / (60 * 60 * 24 * 365.25) );

        //Получаем данные из JSON
        $date_msg = $message_array_json ['date'] ?? 0;
        $id_msg = $message_array_json ['id'] ?? 0;
        $out = $message_array_json ['out'] ?? 0;
        $peer_id = $message_array_json ['peer_id'] ?? 0;
        $text = $message_array_json ['text'] ?? '';
        $text_str = mb_strtolower($text) ?? '';
        $text_explode_command = explode("/", $text_str) ?? '';
        $text_explode_param = explode(":", $text_explode_command ['1']) ?? '';
        $text_explode = explode(":", $text) ?? '';
        $conversation_message_id = $message_array_json ['conversation_message_id'] ?? 0;
        //ARRAY
        $fwd_messages = $message_array_json ['fwd_messages'] ?? '';
        $important = $message_array_json ['important'] ?? '';
        $random_id_json = $message_array_json ['random_id'] ?? 0;
        //ARRAY
        $attachments = $message_array_json ['attachments'] ?? '';
        $payload = $message_array_json ['payload'] ?? '';
        $is_hidden = $message_array_json ['is_hidden'] ?? '';
        $reply_message = $message_array_json ['reply_message'] ?? ''; //Ответ на сообщение
        $reply_date = $reply_message ['date'] ?? '';
        $reply_from_id = $reply_message ['from_id'] ?? 0;
        $reply_text = $reply_message ['text'] ?? '';
        $reply_attachments = $reply_message ['attachments'] ?? '';
        $reply_conversation_message_id = $reply_message ['conversation_message_id'] ?? 0;
        $reply_peer_id = $reply_message ['peer_id'] ?? 0;
        $reply_message_id = $reply_message ['id'] ?? 0;

        $client_info = $object ['client_info'] ?? [];
        //ARRAY
        $button_actions = $client_info ['button_actions'] ?? '';
        $keyboard = $client_info ['keyboard'] ?? '';
        $inline_keyboard = $client_info ['inline_keyboard'] ?? '';
        $carousel = $client_info ['carousel'] ?? '';
        $lang_id = $client_info ['lang_id'] ?? 0;


        if ($payload) {
            $payload = json_decode($payload, true);
        }


        if ($user_id != $peer_id) {
            $type_msg = "beseda";
        } elseif ($user_id == $peer_id) {
            $type_msg = "ls";
        }

        $count = count($button_actions);
        if ($count >= 1) {
            for ($i = 0; $i <= $count; $i++) {
                if ((isset($button_actions[$i])) && ($button_actions[$i] == 'text')) {
                    $user_kbd ['text'] = 1;
                    break;
                } else {
                    $user_kbd ['text'] = 0;
                }
            }

            for ($i = 0; $i <= $count; $i++) {
                if ((isset($button_actions[$i])) && ($button_actions[$i] == 'vkpay')) {
                    $user_kbd ['vkpay'] = 1;
                    break;
                } else {
                    $user_kbd ['vkpay'] = 0;
                }
            }

            for ($i = 0; $i <= $count; $i++) {
                if ((isset($button_actions[$i])) && ($button_actions[$i] == 'open_app')) {
                    $user_kbd ['open_app'] = 1;
                    break;
                } else {
                    $user_kbd ['open_app'] = 0;
                }
            }

            for ($i = 0; $i <= $count; $i++) {
                if ((isset($button_actions[$i])) && ($button_actions[$i] == 'location')) {
                    $user_kbd ['location'] = 1;
                    break;
                } else {
                    $user_kbd ['location'] = 0;
                }
            }

            for ($i = 0; $i <= $count; $i++) {
                if ((isset($button_actions[$i])) && ($button_actions[$i] == 'open_link')) {
                    $user_kbd ['open_link'] = 1;
                    break;
                } else {
                    $user_kbd ['open_link'] = 0;
                }
            }

            for ($i = 0; $i <= $count; $i++) {
                if ((isset($button_actions[$i])) && ($button_actions[$i] == 'open_photo')) {
                    $user_kbd ['open_photo'] = 1;
                    break;
                } else {
                    $user_kbd ['open_photo'] = 0;
                }
            }

            for ($i = 0; $i <= $count; $i++) {
                if ((isset($button_actions[$i])) && ($button_actions[$i] == 'callback')) {
                    $user_kbd ['callback'] = 1;
                    break;
                } else {
                    $user_kbd ['callback'] = 0;
                }
            }

            if ((isset($inline_keyboard)) && ($inline_keyboard == 'true')) {
                $user_kbd ['inline'] = 1;
            } else {
                $user_kbd ['inline'] = 0;
            }

            if (isset($carousel) && $carousel == 1) {
                $user_kbd ['carousel'] = 1;
            } else {
                $user_kbd ['carousel'] = 0;
            }

            if (isset($keyboard) && $keyboard == 1) {
                $user_kbd ['keyboard'] = 1;
            } else {
                $user_kbd ['keyboard'] = 0;
            }

            if (isset($lang_id) && $lang_id == 0) {
                $lang = 'ru';
            } elseif (isset($lang_id) && $lang_id == 1) {
                $lang = 'en';
            }


            if ($text_str == "привет" || $text_str == "начать" || $text_str == "старт") {

                $msg = "Привет вездекодерам! 🥰";

                $kbd = [
                    'inline' => true,
                    'buttons' => [
                        [kbd_text("Привет 🥰",  COLOR_POSITIVE, array_kbd("kbd", "hi", "bot", 1))],
                        [kbd_text("Пока 👹",  COLOR_NEGATIVE, array_kbd("kbd", "bye", "bot", 1))],
                    ]
                ];

                messages_send_kbd($user_id, $msg, $kbd);
                exit ('ok');

            } elseif ($text_str == "мем" || $text_str == "мемы") {

                if ($user ['meme'] == 'true') {
                    $msg = "Выберите одно из действий 🙃";
                } else {
                    $msg = "Добро пожаловать в раздел мемасиков.\n\nЗдесь ты можешь оценить мемчики 2020-ого года из марафона \"Вездекод\", или загрузить свой мемчик, который увидят и смогут оценить другие пользователи 😎";
                }

                $kbd = [
                    'inline' => true,
                    'buttons' => [
                        [kbd_callback("Посмотреть мемы",  COLOR_POSITIVE, array_kbd("meme", "", "bot", 1))],
                        [kbd_callback("Добавить свой мем",  COLOR_PRIMARY, array_kbd("meme", "", "bot", 4))],
                        [kbd_callback("Статистика",  COLOR_PRIMARY, array_kbd("static", "", "bot", 1))],
                    ]
                ];

                messages_send_kbd($user_id, $msg, $kbd);
                exit ('ok');

            } elseif ($text_str == "бд") {
                $msg = "Даю тебе доступ к БД : \n\nСайт : sbots.ru/sql/\n\nЛогин : vezdekod\nПароль : VKCOMvezdekod2022";
                messages_send($user_id, $msg);
            } elseif ($user ['act'] == "") {
                $msg = "Такому меня не учили 🥴";
                messages_send($user_id, $msg);
            }

            if ($user ['act'] == "meme:add") {

                $media_id_vk = $attachments[0]['photo']['id'];
                if (isset($attachments[0]['photo']['sizes'][9]['url'])) {
                    $link = $attachments[0]['photo']['sizes'][9]['url'];
                } else {
                    $link = $attachments[0]['photo']['sizes'][6]['url'];
                }

                $owner_id_vk = $attachments[0]['photo']['owner_id'];

                //1. Загружаем фотографию с сервера ВКонтакте на наш сервер.
                $file = file_get_contents($link);
                $filename = "./meme/photo".$owner_id_vk."_".$media_id_vk.".png";
                $filename_bd = "photo".$owner_id_vk."_".$media_id_vk.".png";
                file_put_contents($filename, $file);

                //2.Отправляем файл на сервер ВКонтакте
                $response = $vk->photos()->getMessagesUploadServer(VK_TOKEN, [
                    'peer_id' => $peer_id
                ]);

                $upload_url = $response ['upload_url'];

                //3. Отправляем фотографию с нашего сервера на сервер ВКонтакте на полученный upload_url
                // Инициализируем cURL
                $ch = curl_init();
                // Поля POST-запроса
                $parameters = [
                    'file' => new CURLFile($filename)  // PHP >= 5.5.0
                    // 'file1' => '@path/to/1.jpg' // PHP < 5.5.0
                ];
                // Ссылка, куда будем загружать картинку - это upload_url
                curl_setopt($ch, CURLOPT_URL, $upload_url);
                // Говорим cURL, что это POST-запрос
                curl_setopt($ch, CURLOPT_POST, true);
                // Говорим cURL, какие поля будем отправлять
                curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
                // Говорим cURL, что нам нужно знать, что ответит сервер, к которому мы будем обращаться
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // Выполняем cURL-запрос. В этой переменной будет JSON-ответ от ВКонтакте
                $curl_result = curl_exec($ch);
                // Закрываем соединение
                curl_close($ch);
                //Сохраняем полученные данные от сервера ВКонтакте в переменные
                $result = json_decode ($curl_result);
                $server = $result->server;
                $photo = $result->photo;
                $hash = $result->hash;

                //4. Передаем предыдущие переменные серверу ВКонтакте для сохранения результата
                $response_save = $vk->photos()->saveMessagesPhoto(VK_TOKEN, [
                    'photo' => $photo,
                    'server' => $server,
                    'hash' => $hash
                ]);
                $response_array = $response_save [0];
                $media_id = $response_save[0]['id'];
                $owner_id = $response_save[0]['owner_id'];

                $attachment = "photo{$owner_id}_{$media_id}";

                meme_add ($user_id, $filename_bd);

                $msg = "Мем успешно загружен. Спасибо ❤️";

                $kbd = [
                    'inline' => true,
                    'buttons' => [
                        [kbd_callback("⬅️ Назад",  COLOR_NEGATIVE, array_kbd("meme", "", "bot", 5))]
                    ]
                ];

                messages_send_attachment_kbd($user_id, $msg, $attachment, $kbd);
                exit ('ok');

            }


            if (is_array($payload)) {

                $name = $payload ['name'] ?? "";
                $act = $payload ['act'] ?? "";
                $section = $payload ['section'] ?? "";
                $cmd = $payload ['cmd'] ?? "";


                if ($section == "bot") {

                    if ($name == "kbd") {

                        if ($cmd == 1) {

                            if ($act == "hi") {

                                $msg = $first_name.", ты с города - \"{$city}\" ";

                                $kbd = [
                                    'inline' => true,
                                    'buttons' => [
                                        [kbd_text("Да 👍🏻",  COLOR_POSITIVE, array_kbd("kbd", "yes", "bot", 2)), kbd_callback("Нет 👎🏻",  COLOR_NEGATIVE, array_kbd("kbd", "no", "bot", 2))]
                                    ]
                                ];

                            } else {

                                $msg = "Я бы обиделся, но Боты не обижаются :)\n\n{$first_name}, ты с города - \"{$city}\" ";

                                $kbd = [
                                    'inline' => true,
                                    'buttons' => [
                                        [kbd_text("Да 👍🏻",  COLOR_POSITIVE, array_kbd("kbd", "yes", "bot", 2)), kbd_text("Нет 👎🏻",  COLOR_NEGATIVE, array_kbd("kbd", "no", "bot", 2))]
                                    ]
                                ];

                            }

                            messages_send_kbd($user_id, $msg, $kbd);
                            exit ('ok');

                        } elseif ($cmd == 2) {

                            if ($act == "yes") {

                                messages_send_sticker($user_id, 64473);

                                if (isset($bdate) && $age != 0) {

                                    $msg = "Ура, я угадал с городом. Теперь попробую угадать возраст...🙈\n\n {$first_name}, тебе  {$age} лет ? ";

                                    $kbd = [
                                        'inline' => true,
                                        'buttons' => [
                                            [kbd_text("Да 👍🏻",  COLOR_POSITIVE, array_kbd("kbd", "age_vk", "bot", 3))],
                                            [kbd_text("Нет, меньше 18-ти",  COLOR_PRIMARY, array_kbd("kbd", "", "bot", 3))],
                                            [kbd_text("Нет, больше 18-ти",  COLOR_PRIMARY, array_kbd("kbd", "", "bot", 3))],
                                        ]
                                    ];

                                } else {

                                    $msg = "Ура, я угадал с городом. Теперь попробую угадать возраст...🙈\n\n {$first_name} Сколько тебе лет ? ";

                                    $kbd = [
                                        'inline' => true,
                                        'buttons' => [
                                            [kbd_text("Меньше 18-ти",  COLOR_POSITIVE, array_kbd("kbd", "", "bot", 3))],
                                            [kbd_text("Больше 18-ти",  COLOR_POSITIVE, array_kbd("kbd", "", "bot", 3))],
                                        ]
                                    ];

                                }

                            } else {

                                if (isset($bdate) && $age != 0) {

                                    $msg = "Пусть город я и не угадал, но все равно попробую угадать твой возраст 🙈\n\n {$first_name}, Вам  {$age} лет ? ";

                                    $kbd = [
                                        'inline' => true,
                                        'buttons' => [
                                            [kbd_text("Да 👍🏻",  COLOR_POSITIVE, array_kbd("kbd", "age_vk", "bot", 3))],
                                            [kbd_text("Нет, меньше 18-ти",  COLOR_PRIMARY, array_kbd("kbd", "", "bot", 3))],
                                            [kbd_text("Нет, больше 18-ти",  COLOR_PRIMARY, array_kbd("kbd", "", "bot", 3))],
                                        ]
                                    ];

                                } else {

                                    $msg = "Пусть город я и не угадал, но все равно попробую угадать твой возраст 🙈\n\n {$first_name} Сколько Вам лет ? ";

                                    $kbd = [
                                        'inline' => true,
                                        'buttons' => [
                                            [kbd_text("Меньше 18-ти",  COLOR_PRIMARY, array_kbd("kbd", "", "bot", 3))],
                                            [kbd_text("Больше 18-ти",  COLOR_PRIMARY, array_kbd("kbd", "", "bot", 3))],
                                        ]
                                    ];

                                }

                            }

                            messages_send_kbd($user_id, $msg, $kbd);
                            exit ('ok');

                        } elseif ($cmd == 3) {

                            if ($act == "age_vk") {

                                messages_send_sticker($user_id,  14152);

                                $msg = "я же говорил, что угадаю 😏 \n\n Как тебе марафон \"Вездекод\" от ВКонтакте ?  ";

                                $kbd = [
                                    'inline' => true,
                                    'buttons' => [
                                        [kbd_text("👍🏻",  COLOR_POSITIVE, array_kbd("kbd", "like", "bot", 4)), kbd_text("👎🏻",  COLOR_NEGATIVE, array_kbd("kbd", "no_like", "bot", 4))]
                                    ]
                                ];

                            } else {

                                $msg = "Эх, не угадал 😔 \n\n Как тебе марафон \"Вездекод\" от ВКонтакте ?  ";

                                $kbd = [
                                    'inline' => true,
                                    'buttons' => [
                                        [kbd_text("👍🏻",  COLOR_POSITIVE, array_kbd("kbd", "like", "bot", 4)), kbd_text("👎🏻",  COLOR_NEGATIVE, array_kbd("kbd", "no_like", "bot", 4))]
                                    ]
                                ];

                            }

                            messages_send_kbd($user_id, $msg, $kbd);
                            exit ('ok');

                        } elseif ($cmd == 4) {

                            if ($act == "like") {

                                messages_send_sticker($user_id,  58232);

                                $msg = "Мне тоже нравится 🥰 Спасибо @team (Команде ВКонтакте) за отличный марафон ❤️\n\nКстати, как думаешь в каком году Команда ВКонтакте придумала марафон \"Вездекод\" ?";

                                $kbd = [
                                    'inline' => true,
                                    'buttons' => [
                                        [kbd_text("2019",  COLOR_PRIMARY, array_kbd("kbd", "2019", "bot", 5)), kbd_text("2020",  COLOR_PRIMARY, array_kbd("kbd", "2020", "bot", 5))],
                                        [kbd_text("2021",  COLOR_PRIMARY, array_kbd("kbd", "2021", "bot", 5)), kbd_text("2022",  COLOR_PRIMARY, array_kbd("kbd", "2022", "bot", 5))],
                                    ]
                                ];

                            } else {

                                $msg = "У каждого своё мнение. Мне вот очень понравилось ❤️\n\nКстати, как думаешь в каком году Команда ВКонтакте придумала марафон \"Вездекод\" ?";

                                $kbd = [
                                    'inline' => true,
                                    'buttons' => [
                                        [kbd_text("2019",  COLOR_PRIMARY, array_kbd("kbd", "2019", "bot", 5)), kbd_text("2020",  COLOR_PRIMARY, array_kbd("kbd", "2020", "bot", 5))],
                                        [kbd_text("2021",  COLOR_PRIMARY, array_kbd("kbd", "2021", "bot", 5)), kbd_text("2022",  COLOR_PRIMARY, array_kbd("kbd", "2022", "bot", 5))],
                                    ]
                                ];

                            }

                            messages_send_kbd($user_id, $msg, $kbd);
                            exit ('ok');

                        } elseif ($cmd == 5) {

                            if ($act == "2020") {

                                messages_send_sticker($user_id,  51593);

                                $msg = "Поздравляю, ты угадал !\n\n\"2020-й не пощадил большинство офлайн-мероприятий — в том числе традиционный VK Hackathon. Раньше мы проводили его в Эрмитаже и Манеже, а в этом году — в паблике ВКонтакте.\" - Открывок из статьи";

                                $link = "https://habr.com/ru/company/vk/blog/526236/";

                                $kbd = [
                                    'inline' => true,
                                    'buttons' => [
                                        [kbd_link($link, "🔥 Статья 🔥")],
                                        [kbd_text("Уже прочитал 😇",  COLOR_PRIMARY, array_kbd("kbd", "yes", "bot", 6))],
                                        [kbd_text("Не интересно 😕",  COLOR_DEFAULT, array_kbd("kbd", "no", "bot", 6))]
                                    ]
                                ];

                            } else {

                                $msg = "Ты был близок... Марафон \"Вездекод\" появился в 2020-ом году\n\n\"2020-й не пощадил большинство офлайн-мероприятий — в том числе традиционный VK Hackathon. Раньше мы проводили его в Эрмитаже и Манеже, а в этом году — в паблике ВКонтакте.\" - Открывок из статьи";

                                $link = "https://habr.com/ru/company/vk/blog/526236/";

                                $kbd = [
                                    'inline' => true,
                                    'buttons' => [
                                        [kbd_link($link, "🔥 Статья 🔥")],
                                        [kbd_text("Уже прочитал 😇",  COLOR_POSITIVE, array_kbd("kbd", "yes", "bot", 6))],
                                        [kbd_text("Не интересно 😕",  COLOR_POSITIVE, array_kbd("kbd", "no", "bot", 6))]
                                    ]
                                ];

                            }

                            messages_send_kbd($user_id, $msg, $kbd);
                            exit ('ok');


                        } elseif ($cmd == 6) {

                            if ($act == "yes") {

                                $msg = "У нас с тобой похожие вкусы 😏 \n\nУчаствовал ли ты до этого в марафоне \"Вездекод\" от Команды ВКонтакте ? ";

                            } else {

                                $msg = "У нас с тобой разные вкусы, это нормально 🙃\n\nУчаствовал ли ты до этого в марафоне \"Вездекод\" от Команды ВКонтакте ? ";

                            }

                            $kbd = [
                                'inline' => true,
                                'buttons' => [
                                    [kbd_text("Конечно",  COLOR_PRIMARY, array_kbd("kbd", "yes", "bot", 7))],
                                    [kbd_text("Нет, первый раз только",  COLOR_PRIMARY, array_kbd("kbd", "no", "bot", 7))]
                                ]
                            ];

                            messages_send_kbd($user_id, $msg, $kbd);
                            exit ('ok');

                        } elseif ($cmd == 7) {

                            if ($act == "yes") {

                                $msg = "Круто ☺️ \n\nМой разработчик участвовал в 2020 году, хотел получаствовать в 2021 году, но из-за работы не успел 🥲, зато успел в 2022 ))\n\n А ты работаешь или учишься ?";

                            } else {

                                $msg = "Ну ничего, все бывает в первый раз ☺️ \n\nМой разработчик участвовал в 2020 году, хотел получаствовать в 2021 году, но из-за работы не успел 🥲, зато успел в 2022 ))\n\n А ты работаешь или учишься ?";

                            }

                            $kbd = [
                                'inline' => true,
                                'buttons' => [
                                    [kbd_text("Работаю",  COLOR_DEFAULT, array_kbd("kbd", "", "bot", 8))],
                                    [kbd_text("Учусь",  COLOR_DEFAULT, array_kbd("kbd", "", "bot", 8))],
                                    [kbd_text("Работаю и учусь",  COLOR_DEFAULT, array_kbd("kbd", "", "bot", 8))]
                                ]
                            ];

                            messages_send_kbd($user_id, $msg, $kbd);
                            exit ('ok');

                        } elseif ($cmd == 8) {

                            $msg = "Ты большой молодец ! Любишь мемы 😂 ?";

                            $kbd = [
                                'inline' => true,
                                'buttons' => [
                                    [kbd_text("Конечно",  COLOR_POSITIVE, array_kbd("kbd", "yes", "bot", 9)), kbd_text("Не-а",  COLOR_NEGATIVE, array_kbd("kbd", "no", "bot", 9))],
                                    [kbd_text("Что это ?",  COLOR_DEFAULT, array_kbd("kbd", "dead", "bot", 9))],
                                ]
                            ];

                            messages_send_kbd($user_id, $msg, $kbd);
                            exit ('ok');

                        } elseif ($cmd == 9) {

                            if ($act == "dead") {
                                messages_send_sticker($user_id,  12690);
                                $msg = "Это такие приколы из одноклассников :) \n\nНапиши мне слово \"Мемы\", и я покажу что это ))";
                            } elseif ($act == "yes") {
                                $msg = "Мы тоже любим мемы. Напиши мне слово \"Мемы\", и мы вместе поугараем с мемов, которые делали участники марафона \"Вездекод\" в 2020 году 😏\n\n Было очень приятно познакомиться с тобой, но на этом наше знакомство подошло к концу ☺";
                            } elseif ($act == "no") {
                                $msg = "Если передумаешь, то Напиши мне слово \"Мемы\", и мы вместе поугараем с мемов, которые делали участники марафона \"Вездекод\" в 2020 году 😏\n\n Было очень приятно познакомиться с тобой, но на этом наше знакомство подошло к концу ☺";
                            }

                            messages_send($user_id, $msg);
                            exit ('ok');

                        }

                    }

                }

            }

        }


    } elseif ($type == "message_event") {

        $user_id = $object ['user_id'] ?? 0;
        $peer_id = $object ['peer_id'] ?? 0;
        $event_id = $object ['event_id'] ?? '';
        $payload = $object ['payload'] ?? '';
        $conversation_message_id = $object ['conversation_message_id'] ?? 0;

        $vk = new VKApiClient(API_VK_VERSION, VKLanguage::RUSSIAN);
        $response = $vk->users()->get(VK_TOKEN, array(
            'user_ids' => $user_id,
            'fields' => array('first_name', 'last_name', 'city', 'bdate'),
        ));
        $first_name = $response[0]['first_name'] ?? '';
        $last_name = $response[0]['last_name'] ?? '';
        $city = $response[0]['city']['title'] ?? 'неизвестный';
        $bdate = $response[0]['bdate'];

        $age = floor( ( time() - strtotime($bdate) ) / (60 * 60 * 24 * 365.25) );

        if (is_array($payload)) {

            $name = $payload ['name'] ?? "";
            $act = $payload ['act'] ?? "";
            $section = $payload ['section'] ?? "";
            $cmd = $payload ['cmd'] ?? "";


            if ($section == "bot") {
                if ($name == "meme") {
                    if ($cmd == 1) {

                        $arr = meme_check($user_id);

                        if ($arr == "max:error") {
                            $msg = "Вы уже посмотрели все мемы))";
                            messages_edit($conversation_message_id, $user_id, $msg);
                            exit ('ok');
                        }

                        $meme_count = $arr ['meme_count'];

                        //1. Загружаем фотографию с сервера ВКонтакте на наш сервер.
                        $filename = "./meme/{$arr ['filename']}";

                        //2.Отправляем файл на сервер ВКонтакте
                        $response = $vk->photos()->getMessagesUploadServer(VK_TOKEN, [
                            'group_id' => GROUP_ID
                        ]);

                        $upload_url = $response ['upload_url'];

                        //3. Отправляем фотографию с нашего сервера на сервер ВКонтакте на полученный upload_url
                        // Инициализируем cURL
                        $ch = curl_init();
                        // Поля POST-запроса
                        $parameters = [
                            'file' => new CURLFile($filename)  // PHP >= 5.5.0
                            // 'file1' => '@path/to/1.jpg' // PHP < 5.5.0
                        ];
                        // Ссылка, куда будем загружать картинку - это upload_url
                        curl_setopt($ch, CURLOPT_URL, $upload_url);
                        // Говорим cURL, что это POST-запрос
                        curl_setopt($ch, CURLOPT_POST, true);
                        // Говорим cURL, какие поля будем отправлять
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
                        // Говорим cURL, что нам нужно знать, что ответит сервер, к которому мы будем обращаться
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        // Выполняем cURL-запрос. В этой переменной будет JSON-ответ от ВКонтакте
                        $curl_result = curl_exec($ch);
                        // Закрываем соединение
                        curl_close($ch);
                        //Сохраняем полученные данные от сервера ВКонтакте в переменные
                        $result = json_decode ($curl_result);

                        if (!$result) {
                            $msg = "Произошла ошибка...";
                            print_r ($curl_result);
                            messages_send($user_id, $msg);
                            exit ('ok');
                        }

                        $server = $result->server;
                        $photo = $result->photo;
                        $hash = $result->hash;

                        //4. Передаем предыдущие переменные серверу ВКонтакте для сохранения результата
                        $response_save = $vk->photos()->saveMessagesPhoto(VK_TOKEN, [
                            'photo' => $photo,
                            'server' => $server,
                            'hash' => $hash
                        ]);
                        $response_array = $response_save [0];
                        $media_id = $response_save[0]['id'];
                        $owner_id = $response_save[0]['owner_id'];

                        $attachment = "photo{$owner_id}_{$media_id}";

                        $msg = "Как Вам этот мем ? 😂";

                        $kbd = [
                            'inline' => true,
                            'buttons' => [
                                [kbd_callback("👍🏻",  COLOR_POSITIVE, array_kbd("meme", $meme_count, "bot", 2)), kbd_callback("👎🏻",  COLOR_NEGATIVE, array_kbd("meme", $meme_count, "bot", 3))],
                                [kbd_callback("Пропустить",  COLOR_DEFAULT, array_kbd("meme", "", "bot", 1))],
                                [kbd_callback("⬅️ Назад",  COLOR_PRIMARY, array_kbd("meme", "", "bot", 5))]
                            ]
                        ];

                        messages_edit_attachment_kbd($conversation_message_id, $user_id, $msg, $attachment, $kbd);
                        exit ('ok');

                    } elseif ($cmd == 2) {

                        meme_like($user_id, $act);

                        $arr = meme_check($user_id);

                        if ($arr == "max:error") {
                            $msg = "Вы уже посмотрели все мемы))";
                            messages_edit($conversation_message_id, $user_id, $msg);
                            exit ('ok');
                        }

                        $meme_count = $arr ['meme_count'];

                        $media_id_vk = $attachments[0]['photo']['id'];
                        if (isset($attachments[0]['photo']['sizes'][9]['url'])) {
                            $link = $attachments[0]['photo']['sizes'][9]['url'];
                        } else {
                            $link = $attachments[0]['photo']['sizes'][6]['url'];
                        }

                        $owner_id_vk = $attachments[0]['photo']['owner_id'];

                        //1. Загружаем фотографию с сервера ВКонтакте на наш сервер.
                        $filename = "./meme/{$arr ['filename']}";

                        //2.Отправляем файл на сервер ВКонтакте
                        $response = $vk->photos()->getMessagesUploadServer(VK_TOKEN, [
                            'group_id' => GROUP_ID
                        ]);

                        $upload_url = $response ['upload_url'];

                        //3. Отправляем фотографию с нашего сервера на сервер ВКонтакте на полученный upload_url
                        // Инициализируем cURL
                        $ch = curl_init();
                        // Поля POST-запроса
                        $parameters = [
                            'file' => new CURLFile($filename)  // PHP >= 5.5.0
                            // 'file1' => '@path/to/1.jpg' // PHP < 5.5.0
                        ];
                        // Ссылка, куда будем загружать картинку - это upload_url
                        curl_setopt($ch, CURLOPT_URL, $upload_url);
                        // Говорим cURL, что это POST-запрос
                        curl_setopt($ch, CURLOPT_POST, true);
                        // Говорим cURL, какие поля будем отправлять
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
                        // Говорим cURL, что нам нужно знать, что ответит сервер, к которому мы будем обращаться
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        // Выполняем cURL-запрос. В этой переменной будет JSON-ответ от ВКонтакте
                        $curl_result = curl_exec($ch);
                        // Закрываем соединение
                        curl_close($ch);
                        //Сохраняем полученные данные от сервера ВКонтакте в переменные
                        $result = json_decode ($curl_result);

                        if (!$result) {
                            $msg = "Произошла ошибка...";
                            print_r ($curl_result);
                            messages_send($user_id, $msg);
                            exit ('ok');
                        }

                        $server = $result->server;
                        $photo = $result->photo;
                        $hash = $result->hash;

                        //4. Передаем предыдущие переменные серверу ВКонтакте для сохранения результата
                        $response_save = $vk->photos()->saveMessagesPhoto(VK_TOKEN, [
                            'photo' => $photo,
                            'server' => $server,
                            'hash' => $hash
                        ]);
                        $response_array = $response_save [0];
                        $media_id = $response_save[0]['id'];
                        $owner_id = $response_save[0]['owner_id'];

                        $attachment = "photo{$owner_id}_{$media_id}";

                        $msg = "Как Вам этот мем ? 😂";

                        $kbd = [
                            'inline' => true,
                            'buttons' => [
                                [kbd_callback("👍🏻",  COLOR_POSITIVE, array_kbd("meme", $meme_count, "bot", 2)), kbd_callback("👎🏻",  COLOR_NEGATIVE, array_kbd("meme", $meme_count, "bot", 3))],
                                [kbd_callback("Пропустить",  COLOR_DEFAULT, array_kbd("meme", "", "bot", 1))],
                                [kbd_callback("⬅️ Назад",  COLOR_PRIMARY, array_kbd("meme", "", "bot", 5))]
                            ]
                        ];

                        messages_edit_attachment_kbd($conversation_message_id, $user_id, $msg, $attachment, $kbd);
                        exit ('ok');

                    } elseif ($cmd == 3) {

                        meme_dislike($user_id, $act);

                        $arr = meme_check($user_id);

                        if ($arr == "max:error") {
                            $msg = "Вы уже посмотрели все мемы))";
                            messages_edit($conversation_message_id, $user_id, $msg);
                            exit ('ok');
                        }

                        $meme_count = $arr ['meme_count'];

                        //1. Загружаем фотографию с сервера ВКонтакте на наш сервер.
                        $filename = "./meme/{$arr ['filename']}";

                        //2.Отправляем файл на сервер ВКонтакте
                        $response = $vk->photos()->getMessagesUploadServer(VK_TOKEN, [
                            'group_id' => GROUP_ID
                        ]);

                        $upload_url = $response ['upload_url'];

                        //3. Отправляем фотографию с нашего сервера на сервер ВКонтакте на полученный upload_url
                        // Инициализируем cURL
                        $ch = curl_init();
                        // Поля POST-запроса
                        $parameters = [
                            'file' => new CURLFile($filename)  // PHP >= 5.5.0
                            // 'file1' => '@path/to/1.jpg' // PHP < 5.5.0
                        ];
                        // Ссылка, куда будем загружать картинку - это upload_url
                        curl_setopt($ch, CURLOPT_URL, $upload_url);
                        // Говорим cURL, что это POST-запрос
                        curl_setopt($ch, CURLOPT_POST, true);
                        // Говорим cURL, какие поля будем отправлять
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
                        // Говорим cURL, что нам нужно знать, что ответит сервер, к которому мы будем обращаться
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        // Выполняем cURL-запрос. В этой переменной будет JSON-ответ от ВКонтакте
                        $curl_result = curl_exec($ch);
                        // Закрываем соединение
                        curl_close($ch);
                        //Сохраняем полученные данные от сервера ВКонтакте в переменные
                        $result = json_decode ($curl_result);

                        if (!$result) {
                            $msg = "Произошла ошибка...";
                            print_r ($curl_result);
                            messages_send($user_id, $msg);
                            exit ('ok');
                        }

                        $server = $result->server;
                        $photo = $result->photo;
                        $hash = $result->hash;

                        //4. Передаем предыдущие переменные серверу ВКонтакте для сохранения результата
                        $response_save = $vk->photos()->saveMessagesPhoto(VK_TOKEN, [
                            'photo' => $photo,
                            'server' => $server,
                            'hash' => $hash
                        ]);
                        $response_array = $response_save [0];
                        $media_id = $response_save[0]['id'];
                        $owner_id = $response_save[0]['owner_id'];

                        $attachment = "photo{$owner_id}_{$media_id}";

                        $msg = "Как Вам этот мем ? 😂";

                        $kbd = [
                            'inline' => true,
                            'buttons' => [
                                [kbd_callback("👍🏻",  COLOR_POSITIVE, array_kbd("meme", $meme_count, "bot", 2)), kbd_callback("👎🏻",  COLOR_NEGATIVE, array_kbd("meme", $meme_count, "bot", 3))]
                            ]
                        ];

                        messages_edit_attachment_kbd($conversation_message_id, $user_id, $msg, $attachment, $kbd);
                        exit ('ok');


                    } elseif ($cmd == 4) {

                        act_edit($user_id, "meme:add");

                        $msg = "Отлично, теперь пришли свой мем 🙈";

                        $kbd = [
                            'inline' => true,
                            'buttons' => [
                                [kbd_callback("⬅️ Назад",  COLOR_NEGATIVE, array_kbd("meme", "", "bot", 5))]
                            ]
                        ];

                        messages_edit_kbd($conversation_message_id, $user_id, $msg, $kbd);
                        exit ('ok');

                    } elseif ($cmd == 5) {

                        act_edit($user_id, "");

                        $msg = "Выберите одно из действий 🙃";

                        $kbd = [
                            'inline' => true,
                            'buttons' => [
                                [kbd_callback("Посмотреть мемы",  COLOR_POSITIVE, array_kbd("meme", "", "bot", 1))],
                                [kbd_callback("Добавить свой мем",  COLOR_PRIMARY, array_kbd("meme", "", "bot", 4))],
                                [kbd_callback("Статистика",  COLOR_PRIMARY, array_kbd("static", "", "bot", 1))],
                            ]
                        ];

                        messages_edit_kbd($conversation_message_id, $user_id, $msg, $kbd);
                        exit ('ok');

                    }
                } elseif ($name == "static") {

                    if ($cmd == 1) {

                        $arr = meme_static($user_id);

                        $meme_count = $arr ['meme_count'];
                        $count = $arr ['count'];
                        $likes = $arr ['likes'];
                        $dislikes = $arr ['dislikes'];

                        $msg = "Ваша статистика : \n\nПросмотрели мемов : {$meme_count} из {$count}\n\nИз них : \n❤️Лайкнули - {$likes}\n💔 Дизлайкнули - {$dislikes}";

                        $kbd = [
                            'inline' => true,
                            'buttons' => [
                                [kbd_callback("Общая Статистика",  COLOR_PRIMARY, array_kbd("static", "", "bot", 2))],
                                [kbd_callback("Топ мемов",  COLOR_PRIMARY, array_kbd("static", "", "bot", 3))],
                                [kbd_callback(" ⬅️ Назад",  COLOR_PRIMARY, array_kbd("meme", "", "bot", 5))],
                            ]
                        ];


                        messages_edit_kbd($conversation_message_id, $user_id, $msg, $kbd);
                        exit ('ok');

                    } elseif ($cmd == 2) {

                        $arr = meme_global_static();

                        $views = $arr ['views'];
                        $count = $arr ['count'];
                        $likes = $arr ['likes'];
                        $dislikes = $arr ['dislikes'];

                        $msg = "Общая статистика : \n\nВсего мемов : {$count}\n👀 Всего просмотров : {$views} \n\nИз них : \n❤️Лайкнули - {$likes}\n💔 Дизлайкнули - {$dislikes}";

                        $kbd = [
                            'inline' => true,
                            'buttons' => [
                                [kbd_callback("Топ мемов",  COLOR_PRIMARY, array_kbd("static", "", "bot", 3))],
                                [kbd_callback("Моя Статистика",  COLOR_PRIMARY, array_kbd("static", "", "bot", 1))],
                                [kbd_callback("⬅️  Назад",  COLOR_PRIMARY, array_kbd("meme", "", "bot", 5))],
                            ]
                        ];

                        messages_edit_kbd($conversation_message_id, $user_id, $msg, $kbd);
                        exit ('ok');

                    } elseif ($cmd == 3) {

                        $arr = meme_top_static();
                        $count = count($arr);

                        $msg_load = "Информация загружается...\n\nПожалуйста подождите 😇";
                        messages_edit($conversation_message_id, $user_id, $msg_load);

                        for ($i=1;$i<=$count;$i++) {

                            //1. Загружаем фотографию с сервера ВКонтакте на наш сервер.
                            $filename = "./meme/".$arr [$i]['file_name'];

                            //2.Отправляем файл на сервер ВКонтакте
                            $response = $vk->photos()->getMessagesUploadServer(VK_TOKEN, [
                                'group_id' => GROUP_ID
                            ]);

                            $upload_url = $response ['upload_url'];

                            //3. Отправляем фотографию с нашего сервера на сервер ВКонтакте на полученный upload_url
                            // Инициализируем cURL
                            $ch = curl_init();
                            // Поля POST-запроса
                            $parameters = [
                                'file' => new CURLFile($filename)  // PHP >= 5.5.0
                                // 'file1' => '@path/to/1.jpg' // PHP < 5.5.0
                            ];
                            // Ссылка, куда будем загружать картинку - это upload_url
                            curl_setopt($ch, CURLOPT_URL, $upload_url);
                            // Говорим cURL, что это POST-запрос
                            curl_setopt($ch, CURLOPT_POST, true);
                            // Говорим cURL, какие поля будем отправлять
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
                            // Говорим cURL, что нам нужно знать, что ответит сервер, к которому мы будем обращаться
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            // Выполняем cURL-запрос. В этой переменной будет JSON-ответ от ВКонтакте
                            $curl_result = curl_exec($ch);
                            // Закрываем соединение
                            curl_close($ch);
                            //Сохраняем полученные данные от сервера ВКонтакте в переменные
                            $result = json_decode ($curl_result);

                            if (!$result) {
                                $msg = "Произошла ошибка...";
                                print_r ($curl_result);
                                messages_send($user_id, $msg);
                                exit ('ok');
                            }

                            $server = $result->server;
                            $photo = $result->photo;
                            $hash = $result->hash;

                            //4. Передаем предыдущие переменные серверу ВКонтакте для сохранения результата
                            $response_save = $vk->photos()->saveMessagesPhoto(VK_TOKEN, [
                                'photo' => $photo,
                                'server' => $server,
                                'hash' => $hash
                            ]);
                            $response_array = $response_save [0];
                            $media_id = $response_save[0]['id'];
                            $owner_id = $response_save[0]['owner_id'];

                            $attachments [$i] = "photo{$owner_id}_{$media_id}";

                            $msg_top [$i] = "{$i} место - ".$arr [$i]['views']." 👀 /".$arr [$i]['likes']." ❤️ /".$arr [$i]['dislikes']." 💔";

                            sleep (0.1);
                        }

                        $msg = "Топ мемов по лайкам : \n\n Место - Просмотров/Лайков/Дизлайков\n".$msg_top [1]."\n".$msg_top [2]."\n".$msg_top [3]."\n".$msg_top [4]."\n".$msg_top [5]."\n".$msg_top [6]."\n".$msg_top [7]."\n".$msg_top [8]."\n".$msg_top [9]."\n";

                        $attachment = $attachments [1].",".$attachments [2].",".$attachments [3].",".$attachments [4].",".$attachments [5].",".$attachments [6].",".$attachments [7].",".$attachments [8].",".$attachments [9];

                        $kbd = [
                            'inline' => true,
                            'buttons' => [
                                [kbd_callback("Моя Статистика",  COLOR_PRIMARY, array_kbd("static", "", "bot", 1))],
                                [kbd_callback("Общая статистика",  COLOR_PRIMARY, array_kbd("static", "", "bot", 2))],
                                [kbd_callback("⬅️ Назад",  COLOR_PRIMARY, array_kbd("meme", "", "bot", 5))],
                            ]
                        ];

                        messages_edit_attachment_kbd($conversation_message_id, $user_id, $msg, $attachment, $kbd);
                        exit ('ok');

                    }

                }
            }

        }

    }

    exit ("ok");
?>