<?php
/**
 * Internationalisation file for Renameuser extension.
 *
 * @package MediaWiki
 * @subpackage Extensions
*/

$wgRenameuserMessages = array();

$wgRenameuserMessages['en'] = array(
	'renameuser'       => 'Rename user',
	'renameuserold'    => 'Current username:',
	'renameusernew'    => 'New username:',
	'renameusersubmit' => 'Submit',
	
	'renameusererrordoesnotexist' => 'The user "<nowiki>$1</nowiki>" does not exist',
	'renameusererrorexists'       => 'The user "<nowiki>$1</nowiki>" already exits',
	'renameusererrorinvalid'      => 'The username "<nowiki>$1</nowiki>" is invalid',
	'renameusererrortoomany'      => 'The user "<nowiki>$1</nowiki>" has $2 contributions, renaming a user with more than $3 contributions could adversely affect site performance',
	'renameusersuccess'           => 'The user "<nowiki>$1</nowiki>" has been renamed to "<nowiki>$2</nowiki>"',
	
	'renameuserlogpage'     => 'User rename log',
	'renameuserlogpagetext' => 'This is a log of changes to user names',
	'renameuserlogentry'    => '', # Don't translate this
	'renameuserlog'         => 'Renamed the user "[[User:$1|$1]]" (which had $3 edits) to "[[User:$2|$2]]"',
);
$wgRenameuserMessages['de'] = array(
	'renameuser'       => 'Benutzer umbenennen',
	'renameuserold'    => 'Bisheriger Benutzername:',
	'renameusernew'    => 'Neuer Benutzername:',
	'renameusersubmit' => 'Umbenennen',
	
	'renameusererrordoesnotexist' => 'Der Benutzername "<nowiki>$1</nowiki>" existiert nicht.',
	'renameusererrorexists'       => 'Der Benutzername "<nowiki>$1</nowiki>" existiert bereits.',
	'renameusererrorinvalid'      => 'Der Benutzername "<nowiki>$1</nowiki>" ist ungültig.',
	'renameusererrortoomany'      => 'Der Benutzer "<nowiki>$1</nowiki>" hat $2 Seitenänderungen. Die Namensänderung eines Benutzers mit mehr als $3 Seitenänderungen kann die Serverleistung nachteilig beeinflussen.',
	'renameusersuccess'           => 'Der Benutzer "<nowiki>$1</nowiki>" wurde erfolgreich umbenannt in "<nowiki>$2</nowiki>".',
	
	'renameuserlogpage'     => 'Benutzernamenänderungs-Logbuch',
	'renameuserlogpagetext' => 'In diesem Logbuch werden die Änderungen von Benutzernamen protokolliert.',
	'renameuserlog'         => 'Benutzer "[[{{ns:user}}:$1|$1]]" (mit $3 Seitenänderungen) umbenannt nach "[[{{ns:user}}:$2|$2]]".',
);
$wgRenameuserMessages['he'] = array(
	'renameuser'       => 'שינוי שם משתמש',
	'renameuserold'    => 'שם משתמש נוכחי:',
	'renameusernew'    => 'שם משתמש חדש:',
	'renameusersubmit' => 'שנה שם משתמש',
	
	'renameusererrordoesnotexist' => 'המשתמש "<nowiki>$1</nowiki>" אינו קיים.',
	'renameusererrorexists'       => 'המשתמש "<nowiki>$1</nowiki>" כבר קיים.',
	'renameusererrorinvalid'      => 'שם המשתמש "<nowiki>$1</nowiki>" אינו תקין.',
	'renameusererrortoomany'      => 'למשתמש "<nowiki>$1</nowiki>" יש $2 תרומות; שינוי שם משתמש של משתמש עם יותר מ־$3 תרומות עלול להשפיע לרעה על ביצועי האתר.',
	'renameusersuccess'           => 'שם המשתמש של המשתמש "<nowiki>$1</nowiki>" שונה לשם "<nowiki>$2</nowiki>"',
	
	'renameuserlogpage'     => 'יומן שינויי שמות משתמש',
	'renameuserlogpagetext' => 'זהו יומן השינויים בשמות המשתמשים.',
	'renameuserlog'         => 'שינה את שם המשתמש "[[{{ns:user}}:$1|$1]]" (שהיו לו $3 עריכות) לשם "[[{{ns:user}}:$2|$2]]"',
);
$wgRenameuserMessages['id'] = array(
	'renameuser'       => 'Penggantian nama pengguna',
	'renameuserold'    => 'Nama sekarang:',
	'renameusernew'    => 'Nama baru:',
	'renameusersubmit' => 'Simpan',
	
	'renameusererrordoesnotexist' => 'Pengguna "<nowiki>$1</nowiki>" tidak ada',
	'renameusererrorexists'       => 'Pengguna "<nowiki>$1</nowiki>" telah ada',
	'renameusererrorinvalid'      => 'Nama pengguna "<nowiki>$1</nowiki>" tidak sah',
	'renameusererrortoomany'      => 'Pengguna "<nowiki>$1</nowiki>" telah memiliki $2 suntingan. Penggantian nama pengguna dengan lebih dari $3 suntingan dapat mempengaruhi kinerja situs',
	'renameusersuccess'           => 'Pengguna "<nowiki>$1</nowiki>" telah diganti namanya menjadi "<nowiki>$2</nowiki>"',
	
	'renameuserlogpage'     => 'Log penggantian nama pengguna',
	'renameuserlogpagetext' => 'Di bawah ini adalah log penggantian nama pengguna',
	'renameuserlog'         => 'Mengganti nama pengguna "[[User:$1|$1]]" (yang telah memiliki $3 suntingan) menjadi "[[User:$2|$2]]"',
);
$wgRenameuserMessages['ja'] = array(
	'renameuser'       => '利用者名の変更',
	'renameuserold'    => '現在の利用者名:',
	'renameusernew'    => '新しい利用者名:',
	'renameusersubmit' => '変更',
	
	'renameusererrordoesnotexist' => '利用者“<nowiki>$1</nowiki>”は存在しません。',
	'renameusererrorexists'       => '利用者“<nowiki>$1</nowiki>”は既に存在しています。',
	'renameusererrorinvalid'      => '利用者名“<nowiki>$1</nowiki>”は無効な値です。',
	'renameusererrortoomany'      => '利用者“$1”には $2 件の投稿履歴があります。$3 件以上の投稿履歴がある利用者の名前を変更すると、サイトのパフォーマンスに悪影響を及ぼす可能性があります。',
	'renameusersuccess'           => '利用者“<nowiki>$1</nowiki>”を“<nowiki>$2</nowiki>”に変更しました。',
	
	'renameuserlogpage'     => '利用者名変更記録',
	'renameuserlogpagetext' => 'これは利用者名の変更を記録したものです。',
	'renameuserlog'         => '利用者 “[[User:$1|$1]]” (投稿数 $3回) を “[[User:$2|$2]]” へ変更しました。',
);
$wgRenameuserMessages['nl'] = array(
	'renameuser'       => 'Gebruiker hernoemen',
	'renameuserold'    => 'Huidige gebruikersnaam:',
	'renameusernew'    => 'Nieuwe gebruikersnaam:',
	'renameusersubmit' => 'Hernoemen',

	'renameusererrordoesnotexist' => 'De gebruiker "<nowiki>$1</nowiki>" bestaat niet',
	'renameusererrorexists'       => 'De gebruiker "<nowiki>$1</nowiki>" bestaat al',
	'renameusererrorinvalid'      => 'De gebruikersnaam "<nowiki>$1</nowiki>" is ongeldig',
	'renameusererrortoomany'      => 'Gebruiker "<nowiki>$1</nowiki>" heeft $2 bewerkingen. Het hernoemen van gebruikers met meer dan $3 bewerkingen kan de snelheid van de wiki nadelig beïnvloeden.',
	'renameusersuccess'           => 'Gebruiker "<nowiki>$1</nowiki>" is hernoemd naar "<nowiki>$2</nowiki>"',

	'renameuserlogpage'     => 'Logboek gebruikersnaamwijzigingen',
	'renameuserlogpagetext' => 'Hieronder staan gebruikersnamen die gewijzigd zijn',
	'renameuserlog'         => 'Gebruiker "[[User:$1|$1]]" (met $3 bewerkingen) is hernoemd naar "[[User:$2|$2]]"',
);
$wgRenameuserMessages['pl'] = array(
	'renameuser'       => 'Zmień nazwę użytkownika',
	'renameuserold'    => 'Obecna nazwa użytkownika:',
	'renameusernew'    => 'Nowa nazwa użytkownika:',
	'renameusersubmit' => 'Zmień',
	
	'renameusererrordoesnotexist' => 'Użytkownik "<nowiki>$1</nowiki>" nie istnieje',
	'renameusererrorexists'       => 'Użytkownik "<nowiki>$1</nowiki>" już istnieje',
	'renameusererrorinvalid'      => 'Nazwa użytkownika "<nowiki>$1</nowiki>" jest nieprawidłowa',
	'renameusererrortoomany'      => 'Użytkownik "<nowiki>$1</nowiki>" ma $2 edycji. Zmiana nazwy użytkownika mającego powyżej $3 edycji może wpłynąć na wydajność serwisu.',
	'renameusersuccess'           => 'Nazwa użytkownika "<nowiki>$1</nowiki>" została zmieniona na "<nowiki>$2</nowiki>"',
	
	'renameuserlogpage'     => 'Zmiany nazw użytkowników',
	'renameuserlogpagetext' => 'To jest rejestr zmian nazw użytkowników',
	'renameuserlog'         => 'Zmieniono nazwę użytkownika "[[User:$1|$1]]" (mającego $3 edycji) na "[[User:$2|$2]]"',
);
$wgRenameuserMessages['pt'] = array(
	'renameuser'       => 'Renomear utilizador',
	'renameuserold'    => 'Nome de utilizador actual:',
	'renameusernew'    => 'Novo nome de utilizador:',
	'renameusersubmit' => 'Enviar',
	
	'renameusererrordoesnotexist' => 'O utilizador "<nowiki>$1</nowiki>" não existe',
	'renameusererrorexists'       => 'O utilizador "<nowiki>$1</nowiki>" já existe',
	'renameusererrorinvalid'      => 'O nome de utilizador "<nowiki>$1</nowiki>" é inválido',
	'renameusererrortoomany'      => 'O utilizador "<nowiki>$1</nowiki>" possui $2 contribuições. Renomear um utilizador com mais de $3 contribuições pode afectar o desempenho do site',
	'renameusersuccess'           => 'O utilizador "<nowiki>$1</nowiki>" foi renomeado para "<nowiki>$2</nowiki>"',
	
	'renameuserlogpage'     => 'Registo de renomeação de utilizadores',
	'renameuserlogpagetext' => 'Este é um registo de alterações efectuadas a nomes de utilizadores',
	'renameuserlog'         => 'Renomeado o utilizador "[[{{ns:user}}:$1|$1]]" (que possuia $3 edições) para "[[{{ns:user}}:$2|$2]]"',
);
$wgRenameuserMessages['ru'] = array(
	'renameuser'       => 'Переименовать участника',
	'renameuserold'    => 'Имя в настоящий момент:',
	'renameusernew'    => 'Новое имя:',
	'renameusersubmit' => 'Выполнить',
	
	'renameusererrordoesnotexist' => 'Участника с именем «<nowiki>$1</nowiki>» не зарегистрировано',
	'renameusererrorexists'       => 'Участник с именем «<nowiki>$1</nowiki>» уже зарегистрирован',
	'renameusererrorinvalid'      => 'Недопустимое имя участника: <nowiki>$1</nowiki>',
	'renameusererrortoomany'      => 'Участник <nowiki>$1</nowiki> внёс $2 правок, переименование участника с более чем $3 правками может оказать негативное влияние на доступ к сайту',
	'renameusersuccess'           => 'Участник «<nowiki>$1</nowiki>» был переименован в «<nowiki>$2</nowiki>»',
	
	'renameuserlogpage'     => 'Журнал переименований участников',
	'renameuserlogpagetext' => 'Это журнал произведённых переименований зарегистрированных участников',
	'renameuserlog'         => 'Участник «[[User:$1|$1]]» (имеющий $3 правок) переименован в «[[User:$2|$2]]»',
);
$wgRenameuserMessages['wa'] = array(
	'renameuser' => 'Rilomer èn uzeu',
	
	'renameuserlog' => 'L\' uzeu «[[Uzeu:$1|$1]]» (k\' aveut ddja fwait $3 candjmints) a stî rlomé a «[[Uzeu:$2|$2]]»',
	'renameuserlogpage' => 'Djournå des candjmints d\' no d\' uzeus',
	'renameuserlogpagetext' => 'Chal pa dzo c\' est ene djivêye des uzeus k\' ont candjî leu no d\' elodjaedje.',
);
$wgRenameuserMessages['zh-cn'] = array(
	'renameuser'       => '用户重命名',
	'renameuserold'    => '当前用户名：',
	'renameusernew'    => '新用户名：',
	'renameusersubmit' => '提交',
	
	'renameusererrordoesnotexist' => '用户"<nowiki>$1</nowiki>"不存在',
	'renameusererrorexists'       => '用户"<nowiki>$1</nowiki>"已存在',
	'renameusererrorinvalid'      => '用户名"<nowiki>$1</nowiki>"不可用',
	'renameusererrortoomany'      => '用户"<nowiki>$1</nowiki>"贡献了$2次，重命名一个超过$3次的用户会影响站点性能',
	'renameusersuccess'           => '用户"<nowiki>$1</nowiki>"已经更名为"<nowiki>$2</nowiki>"',
	
	'renameuserlogpage'     => '用户名变更日志',
	'renameuserlogpagetext' => '这是用户名更改的日志',
	'renameuserlog'         => '已重命名用户 "[[User:$1|$1]]" (拥有$3次编辑) 为 "[[User:$2|$2]]"',
);
$wgRenameuserMessages['zh-tw'] = array(
	'renameuser'       => '用戶重新命名',
	'renameuserold'    => '現時用戶名：',
	'renameusernew'    => '新用戶名：',
	'renameusersubmit' => '提交',
	
	'renameusererrordoesnotexist' => '用戶"<nowiki>$1</nowiki>"不存在',
	'renameusererrorexists'       => '用戶"<nowiki>$1</nowiki>"已存在',
	'renameusererrorinvalid'      => '用戶名"<nowiki>$1</nowiki>"不可用',
	'renameusererrortoomany'      => '用戶"<nowiki>$1</nowiki>"貢獻了$2次，重新命名一個超過$3次的用戶會影響網站效能',
	'renameusersuccess'           => '用戶"<nowiki>$1</nowiki>"已經更名為"<nowiki>$2</nowiki>"',
	
	'renameuserlogpage'     => '用戶名變更日誌',
	'renameuserlogpagetext' => '這是用戶名更改的日誌',
	'renameuserlog'         => '已重新命名用戶 "[[User:$1|$1]]" (擁有$3次編輯) 為 "[[User:$2|$2]]"',
);
$wgRenameuserMessages['zh-yue'] = array(
	'renameuser'       => '改用戶名',
	'renameuserold'    => '現時嘅用戶名：',
	'renameusernew'    => '新嘅用戶名：',
	'renameusersubmit' => '遞交',
	
	'renameusererrordoesnotexist' => '用戶"<nowiki>$1</nowiki>"唔存在',
	'renameusererrorexists'       => '用戶"<nowiki>$1</nowiki>"已經存在',
	'renameusererrorinvalid'      => '用戶名"<nowiki>$1</nowiki>"唔正確',
	'renameusererrortoomany'      => '用戶"<nowiki>$1</nowiki>"貢獻咗$2次，對改一個超過$3次的用戶名嘅用戶可能會影響網站嘅效能',
	'renameusersuccess'           => '用戶"<nowiki>$1</nowiki>"已經改咗名做"<nowiki>$2</nowiki>"',
	
	'renameuserlogpage'     => '用戶改名日誌',
	'renameuserlogpagetext' => '呢個係改用戶名嘅日誌',
	'renameuserlog'         => '已經將用戶 "[[User:$1|$1]]" (擁有$3次編輯) 改名做 "[[User:$2|$2]]"',
);
$wgRenameuserMessages['zh-hk'] = $wgRenameuserMessages['zh-tw'];
$wgRenameuserMessages['zh-sg'] = $wgRenameuserMessages['zh-cn'];
?>
