<?php
/**
 * Internationalization file for Renameuser extension.
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

?>
