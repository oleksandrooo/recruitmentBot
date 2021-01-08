<?php

return [
    'startSearchProjectsText' => 'Коротко про те, як шукати:' . PHP_EOL . 'Вводити англійською мовою через кому в 
        довільному порядку ключові слова. Якщо треба проекти літом/весною/зимою/ще колись, то пиши summer/spring/winter.
         Якщо це певний проміжок часу, то пиши через дефіс який день, місяць і обов`язково вказуй рік. Якщо треба 
         вказати SDG - пишеш "sdg 2", де 2 - номер потрібної цілі, так само пишеш "meal 1", де meal - скільки раз на
          день кормлять, якщо без харчування, то "meal 0". Декілька прикладів:' . PHP_EOL . PHP_EOL . 'Turkey, sdg 4, 
          01.06.2021-01.08.2021, adaptability' . PHP_EOL . 'summer, italy, torino, sdg 10' . PHP_EOL . PHP_EOL .
        'Країни: Turkey, India, Romania, Greece, Italy, Brazil' . PHP_EOL . "LC: salvador, auth, kocaeli, delhi iit,
          chennai, ankara, kutahya, konya, sakarya, dehradun, trento, nfii, milano, bologna, torino, istanbul, izmir,
          mahe, Delhi university, Bengaluru, Pitesti, Bucharest, Constanta" . PHP_EOL . 'Можливі навички:
          adaptability, leadership, training' . PHP_EOL . PHP_EOL . 'По країнах, лк і sdg шукає норм, а там як
          повезе))' . PHP_EOL . 'Якщо треба припинити пошук - пиши /cancel',
    'meeting' => 'З поверненням🥳',
    'pageInDevelop' => 'В розробці',
    'writeAdminText' => 'Дуже мало часу робити ще робити якусь спеціальне меню щоб написати, тому просто пиши 
        @oleksandrooo .Якщо що постарайся детальніше описати проблему, щоб скоріше пофіксити, бо на BF швидкість 
        дуже важлива' . PHP_EOL . PHP_EOL . 'За дизайн не бубніти, головне щоб працювало ☝',
    'FAQtextSend' => 'Тут можна найти посилання на форми щоб зробити договір, інвойс на оплату, файлики, 
        скрипти відповідей на загальні якісь питання, відповіді на відмови, питання по конкретних країнах, щоб хоч 
        трохи економити час і не писати самому. Можна і свою кнопку з відповіддю додати',
    'addButtonText' => 'Напиши спочатку назву кнопочки, потім дві двокрапки, пробіл, і напиши текст. Приклад:' .
        PHP_EOL . PHP_EOL . 'Я гарна кнопочка, нажми на мене☺️☺️:: Щастя тобі і гарного дня💛💛' . PHP_EOL . PHP_EOL .
        'А якщо не треба додавати кнопку, то напиши /cancel',
    'newButtonSaved' => 'Зберіг кнопку',
    'buttonWasDeleted' => 'Видалив кнопку',
    'mainMenuText' => 'Головне меню',
    'cancel' => 'Скасовано',
    'showProjectsMenuText' => 'Це святая святих - фільтр проектів. Коротко про те, як ним користуватися' . PHP_EOL .
        'Побачив в ір чаті що є новий проект - не лінися, тицяй "➕Новий проект"' . PHP_EOL . 'Є декілька шаблонів 
            пошуку, можеш скористуватися ними, але якщо не підходять, то тицяй "Шукати проекти" і шукай' . PHP_EOL .
        PHP_EOL . 'Прохання сильно не докопуватися до того як шукає, я це написав в 4 ранку, майте совість, я потім
             все підправлю' . PHP_EOL . 'Удачі знайти щось годне',
    'addNewProjectsSession' => '❗️❗️❗️Проекти за посиланням типу https://aiesec.org/opportunity/1228366, де немає 
        "global-volunteer" можуть не додаватися. Найближчими днями пофікшу' . PHP_EOL . 'Надішли посилання на проекти, 
        які розділені переносом на нову стрічку. Якщо проект є в базі, то він просто не додасться. На всякий приклад 
        того, як відправляти:' . PHP_EOL . PHP_EOL . 'https://aiesec.org/opportunity/global-volunteer/1262037' .
        PHP_EOL . 'https://aiesec.org/opportunity/global-volunteer/1262036' . PHP_EOL . PHP_EOL . '/cancel якщо 
         передумав(ла) додавати проекти',
    'startDownloadingProject' => 'Підключаюсь до aiesec.org і починаю завантажувати інформацію про проект(и)...' .
        PHP_EOL . PHP_EOL . 'Якщо зависло - натискай на /cancel',
    'endDownloadingProject' => 'Проект(и) збережені в базі даних. Ось на них посилання:' . PHP_EOL . PHP_EOL,
    'processedCountProject' => 'Оброблено %d/%d проектів',
    'emptyInfoAboutProjectList' => 'Ці проекти вже є в базі даних',
    'downloadingProjectError' => 'Проект https://aiesec.org/opportunity/global-volunteer/%d не існує, закритий, або
         вже збережений у базі',
    'getProjectError' => 'Такого проекту не існує',
    'getTextProjectID' => '⚪️Link:' . PHP_EOL . 'https://aiesec.org/opportunity/global-volunteer/%d',
    'getTextProjectDescription' => '⚪️Description: ',
    'getTextProjectLanguages' => '⚪️Languages: ',
    'getTextProjectProjectDescription' => '⚪️Project description: ',
    'getTextProjectLearningPoints' => '⚪️Learning points: ',
    'getTextProjectSkills' => '⚪️Skills: ',
    'getTextProjectSDG' => '⚪️SDG: ',
    'getTextProjectCountry' => '⚪️Country: ',
    'getTextProjectHomeLC' => '⚪️Home LC: ',
    'getTextProjectLocation' => '⚪️Location: ',
    'getTextProjectTitle' => '⚪️Title: ',
    'getTextProjectProjectName' => '⚪️Project name: ',
    'getTextProjectMealCount' => '⚪️Meal count: ',
    'getTextProjectWorkHours' => '⚪️Work hours: ',
    'getTextProjectAvailableSlots' => '⚪️Available slots: ',
    'getTextProjectBackgrounds' => '⚪️Backgrounds: ',
    'getTextProjectEmptyAvailableSlots' => 'Empty',
    'allProjectsCount' => 'Усього проектів: ',
    'emptyAvailableProjects' => 'За такими ключовими словами в базі проектів немає.
         Можеш спрообувати пошукати в ір тулі'
];
