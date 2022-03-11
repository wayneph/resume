## sitte_static
SELECT * FROM site_static WHERE (site_id=8 AND status=1) ORDER BY seq;       ##tsc=9 tek=9

## websites Select Pages by SiteID
SELECT * FROM pages  WHERE site_id=8 ORDER BY seq;      ##tsc=8
SELECT * FROM pages  WHERE site_id=9 ORDER BY seq;      ##tek=9

## pwaInstruct =40 from 25

## by slug
SELECT * FROM pages  WHERE slug='listEntityTypes';                ##index

SELECT * FROM page_elements WHERE (page_id = 28 AND status = 1) ORDER BY seq       ## 28->41


INSERT INTO `page_elements` (`page_id`, `seq`, `conditional`, `position_name`, `element_type`, `element_text`, `created`, `updated`, `status`) VALUES
(41, 0, 0, 'menu', 1, '<li><a href=\"index.php\">Return</a></li>\r\n###softSubMenus###\r\n<li><a href=\"pwaInstruct.php\">Get App on phone</a></li>', '2021-10-13 12:48:04', '2022-02-21 12:52:46', 1),
(41, 12, 0, 'postsHeading', 1, '<h2><strong>Last 3 Comments</strong></h2>', '2021-10-13 12:48:28', '2021-10-13 12:48:28', 1),
(41, 20, 0, 'heading', 1, '<h2><strong>&nbsp;TSC&nbsp;</strong>Entities<strong> Classification </strong></h2>', '2021-10-13 12:48:47', '2022-02-21 12:27:04', 1),
(41, 55, 0, 'touchUs', 1, '<h2>\r\n  <b>\r\n   Touch Us:\r\n  </b>\r\n  ###touchOptions###\r\n</h2>', '2022-02-28 13:31:26', '2022-02-28 13:33:04', 1),
(41, 60, 0, 'pictureFooter', 1, '<header>\r\n  <h3>\r\n    Steering Your Tech Ship \r\n  </h3>\r\n</header>\r\n<p>\r\n  Kubernetes <b>is Greek for </b>Helmsman\r\n</p>', '2021-10-13 12:48:58', '2021-10-13 12:48:58', 1),
(41, 60, 0, 'contactForm', 1, '<form action=\"switch.php\" method=\"post\">\r\n  <input type=\"hidden\" name=\"pstHash\" value=\"###userHash###\">\r\n  <input type=\"hidden\" name=\"pstSource\" value=\"contactForm\">\r\n  <input type=\"hidden\" name=\"pstFrom\" value=\"###myName###\">\r\n  <input type=\"hidden\" name=\"pstType\" value=\"contactForm\">\r\n  <div class=\"row gtr-50\">\r\n    <div class=\"col-6 col-12-small\">\r\n      ###login###\r\n      <select id=\"feedback\" name=\"pstCategory\">\r\n        <option value=\"none\">..Select..</option>\r\n        <option value=\"NewPin\">Reset My PIN to..</option>\r\n        <option value=\"Comment\">Comment</option>\r\n        <option value=\"Question\">Question</option>\r\n        <option value=\"Suggestion\">Suggestion</option>\r\n        <option value=\"Bug\">Found an app bug</option>\r\n     </select>\r\n    </div>\r\n    <div class=\"col-12\">\r\n      <textarea name=\"pstMessage\" placeholder=\"Message\"></textarea>\r\n    </div>\r\n    <div class=\"col-12\">\r\n     <input type=\"submit\" value=\"Touch Us!\">\r\n    </div>\r\n  </div>\r\n</form>', '2022-02-28 13:31:43', '2022-03-01 14:07:56', 1),
(41, 65, 0, 'loginForm', 1, '<form action=\"switch.php\" method=\"post\">\r\n  <input type=\"hidden\" name=\"pstHash\" value=\"###userHash###\">\r\n  <input type=\"hidden\" name=\"pstSource\" value=\"###myName###\">\r\n  <input type=\"hidden\" name=\"pstType\" value=\"loginForm\">\r\n  <div class=\"row gtr-50\">\r\n    <div class=\"col-6 col-12-small\">\r\n      <input name=\"pstFrom\" placeholder=\"Name\" type=\"text\" value=\"\">\r\n    </div>\r\n    <div class=\"col-6 col-12-small\">\r\n        <input name=\"pstMail\" placeholder=\"Email\" type=\"text\" value=\"\">\r\n    </div>\r\n    <div class=\"col-6 col-12-small\">\r\n      <select id=\"feedback\" name=\"pstCategory\">\r\n        <option value=\"none\">..Select..</option>\r\n        <option value=\"login\">Login with PIN</option>\r\n        <option value=\"register\">Register with PIN</option>\r\n     </select>\r\n    </div>\r\n    <div class=\"col-6 col-12-small\">\r\n      <input name=\"pstPIN\" placeholder=\"PIN\" type=\"text\" value=\"PIN\">\r\n    </div>\r\n    <div class=\"col-12\">\r\n     <input type=\"submit\" value=\"Touch Us!\">\r\n    </div>\r\n  </div>\r\n</form>', '2022-02-28 13:31:57', '2022-03-01 14:08:04', 1),
(41, 80, 0, 'footTextHeader', 1, '<h2><strong>Container Orchestration</strong></h2>', '2021-10-13 12:49:07', '2021-10-13 12:49:07', 1),
(41, 99, 0, 'scriptAdded', 10, '<script>\r\nvar acc = document.getElementsByClassName(\"accordion\");\r\nvar i;\r\n\r\nfor (i = 0; i < acc.length; i++) {\r\n  acc[i].addEventListener(\"click\", function() {\r\n    this.classList.toggle(\"active\");\r\n    var panel = this.nextElementSibling;\r\n    if (panel.style.display === \"block\") {\r\n      panel.style.display = \"none\";\r\n    } else {\r\n      panel.style.display = \"block\";\r\n    }\r\n  });\r\n}\r\n</script>', '2021-10-13 12:49:17', '2021-10-13 12:49:17', 1),
(41, 100, 0, 'footText', 1, 'Help with text -- Maybe we need to say something about our privacy.\r\n<br><br>\r\nMore Help Settings allow for <b>authentication </b>  I.e. Usernames, pins and passwords.', '2021-10-13 12:49:29', '2022-02-25 08:09:34', 1);




##
INSERT INTO `site_static` (`seq`, `site_id`, `position_name`, `ht`, `created`, `updated`, `status`) VALUES
(0, 9, 'siteLogo', '<h1 id=\"logo\">\r\n  <a href=\"index.php\">Theewaters Sport Club</a>\r\n</h1>\r\n<p>\r\n  for <b>Boating, Fishing, Swimming, Camping</b> & general Chilling!\r\n</p>', '2021-09-21 16:28:40', '2022-02-25 08:01:38', 1),
(6, 9, 'copyRight', '<li>\r\n  &copy; : Theewaters Sport Club Members(2021).\r\n</li>\r\n<li>\r\n  Powered by : <a href=\"http://skunks.co\" target=\"_blank\">SkunkWorx</a>\r\n</li>\r\n<li>\r\n  Design : Wayne Philip.\r\n  Data : David Fourie\r\n</li>', '2021-09-21 16:36:44', '2022-02-25 07:59:59', 1);



INSERT INTO `page_elements` ( `page_id`, `seq`, `conditional`, `position_name`, `element_type`, `element_text`, `created`, `updated`, `status`) VALUES
(37, 1, 0, 'menu', 1, '<li><a href=\"index.php\">The Club</a></li>\r\n###softSubMenus###\r\n<li><a href=\"pwaInstruct.php\">Get App on phone</a></li>\r\n\r\n\r\n\r\n', '2021-09-15 14:19:13', '2022-02-27 10:50:43', 1),
(37, 40, 0, 'pictureHeader', 1, '<h2><strong>&nbsp;Our Club&nbsp;</strong> Our Passion(s) <strong>!</strong></h2>', '2021-09-21 16:58:13', '2022-02-27 07:20:59', 1),
(37, 50, 1, 'randpicno', 1, 'rand|\r\n1~10', '2021-09-17 16:19:57', '2022-02-27 07:20:48', 1),
(37, 52, 0, 'pictureFooter', 1, '<header>\r\n  <h3>\r\n    One of our Facilities or Activities\r\n  </h3>\r\n</header>\r\n<p>-\r\n  Something for ALL!\r\n</p>', '2021-09-21 17:00:34', '2022-02-27 10:52:32', 1),
(37, 55, 0, 'touchUs', 1, '<h2>\r\n  <b>\r\n   Touch Us:\r\n  </b>\r\n  ###touchOptions###\r\n</h2>', '2022-02-27 10:45:34', '2022-02-27 10:46:11', 1),
(37, 60, 0, 'contactForm', 1, '<form action=\"switch.php\" method=\"post\">\r\n  <input type=\"hidden\" name=\"pstHash\" value=\"###userHash###\">\r\n  <input type=\"hidden\" name=\"pstSource\" value=\"contactForm\">\r\n  <input type=\"hidden\" name=\"pstFrom\" value=\"###myName###\">\r\n  <input type=\"hidden\" name=\"pstType\" value=\"contactForm\">\r\n  <div class=\"row gtr-50\">\r\n    <div class=\"col-6 col-12-small\">\r\n      ###login###\r\n      <select id=\"feedback\" name=\"pstCategory\">\r\n        <option value=\"none\">..Select..</option>\r\n        <option value=\"NewPin\">Reset My PIN to..</option>\r\n        <option value=\"Comment\">Comment</option>\r\n        <option value=\"Question\">Question</option>\r\n        <option value=\"Suggestion\">Suggestion</option>\r\n        <option value=\"Bug\">Found an app bug</option>\r\n     </select>\r\n    </div>\r\n    <div class=\"col-12\">\r\n      <textarea name=\"pstMessage\" placeholder=\"Message\"></textarea>\r\n    </div>\r\n    <div class=\"col-12\">\r\n     <input type=\"submit\" value=\"Touch Us!\">\r\n    </div>\r\n  </div>\r\n</form>', '2022-02-27 07:19:06', '2022-03-01 13:54:32', 1),
(37, 65, 0, 'loginForm', 1, '<form action=\"switch.php\" method=\"post\">\r\n  <input type=\"hidden\" name=\"pstHash\" value=\"###userHash###\">\r\n  <input type=\"hidden\" name=\"pstSource\" value=\"###myName###\">\r\n  <input type=\"hidden\" name=\"pstType\" value=\"loginForm\">\r\n  <div class=\"row gtr-50\">\r\n    <div class=\"col-6 col-12-small\">\r\n      <input name=\"pstFrom\" placeholder=\"Name\" type=\"text\" value=\"\">\r\n    </div>\r\n    <div class=\"col-6 col-12-small\">\r\n        <input name=\"pstMail\" placeholder=\"Email\" type=\"text\" value=\"\">\r\n    </div>\r\n    <div class=\"col-6 col-12-small\">\r\n      <select id=\"feedback\" name=\"pstCategory\">\r\n        <option value=\"none\">..Select..</option>\r\n        <option value=\"login\">Login with PIN</option>\r\n        <option value=\"register\">Register with PIN</option>\r\n     </select>\r\n    </div>\r\n    <div class=\"col-6 col-12-small\">\r\n      <input name=\"pstPIN\" placeholder=\"PIN\" type=\"text\" value=\"PIN\">\r\n    </div>\r\n    <div class=\"col-12\">\r\n     <input type=\"submit\" value=\"Touch Us!\">\r\n    </div>\r\n  </div>\r\n</form>', '2022-02-27 07:21:34', '2022-03-01 13:54:46', 1),
(37, 80, 0, 'footTextHeader', 1, '<h2>This App is for the management of <strong>Member information</strong></h2>', '2021-09-21 17:03:03', '2022-02-27 11:51:41', 1),
(37, 100, 0, 'footText', 1, 'Members are authenticated with the eMail address we have on file.\r\n<br><br>\r\nA login with the PIN we will send you is valid for 5 days.\r\n<br><br>\r\nIf authenticated you may leave Messages, Read Messages left for you, change your PIN etc..\r\n<br><br>Do not loose your PIN please &amp; reset it if you need to. ', '2021-09-20 18:06:01', '2022-03-06 07:05:00', 1);
