<?php

define("_Msg_Language", "لطفا زبان ربات را انتخاب کنید:");
$msgHelp = "قابلیت‌های این ربات به شرح زیر است:" . "\n \n";
$msgHelp .= "📌 قابلیت‌های بدون دستور ربات:" . "\n";
$msgHelp .= "- <b>" . "جزییات حساب توییتری" . "</b>: " . "شما می‌توانید با ارسال آی‌دی کاربران توییتر (ماننده @jack یا jack) جزییات حساب آن‌ها مانند سن توییتری، تعدادفیوها، تعداد توییت‌ها، تعداد فالوینگ و طرفداران و ... را مشاهده کنید." . "\n \n";
$msgHelp .= "- <b>" . "دانلود ویدیو" . "</b>: " . "همچنین برای دانلود ویدیوهای داخل توییتر فقط کافی است لینک توییت حاوی ویدیو را برای ربات ارسال کنید و بعد فایل ویدیو را به راحتی تماشا و یا دانلود کنید." . "\n \n";
$msgHelp .= "📌 قابلیت‌های دستوری ربات:" . "\n";
$msgHelp .= "شروع ربات (/start): با استفاده از این دستور میتوانید وارد ربات شوید." . "\n \n";
$msgHelp .= "حساب کاربری (/profile): این دستور جزییاتی از ورود شما به ربات، افراد دعوت شده بوسیله شما، امتیاز کاربری و ... مربوط به اکانت شما را نمایش میدهد." . "\n \n";
$msgHelp .= "اتصال حساب (/connect): شما میتوانید بدون ارائه رمز حساب توییتری خود به ربات متصل شوید، این ربات به حریم خصوصی و اطلاعات حساب شما دسترسی ندارد!" . "\n \n";
$msgHelp .= "آنفالویاب (/unfollower): بعد از اتصال حساب می‌توانید افرادی که شما را آنفالو کردن را شناسایی یا فرد مجاز طبقه‌بندی کنید!" . "\n \n";
//$msgHelp .= "طرفداران (/unfollowing): بعد از اتصال حساب می‌توانید افرادی که منتظر فالو شدن بوسیله شما می‌باشند را شناسایی و فالو کنید." . "\n \n";
$msgHelp .= "تست شدوبن (/shadowban): بعد از اتصال حساب می‌توانید در بازه‌های معین استعلام محدودیت شدوبن از حساب خود بوسیله ربات دریافت کنید." . "\n \n";
$msgHelp .= "لیست افراد مجاز (/whitelist): شما می‌توانید افراد مجازی که با استفاده از دستور آنفالویاب و طرفداران به ربات معرفی کردید را با استفاده از این دستور مشاهده و یا ویرایش کنید." . "\n \n";
$msgHelp .= "لیست ویدیوها (/videolist): شما میتوانید با ارسال لینک توییت حاوی ویدیو و دانلود آن، همچنین تاریخچه دانلودها را در این قسمت مشاهده کنید و در صورت تمایل دوباره فایل را درخواست کنید!" . "\n \n";
$msgHelp .= "آمار ربات (/statistics): تاریخ ساخت ربات، سن ربات، ورژن ربات و تعداد افراد استفاده کننده از این ربات در این دستور قابل مشاهده‌اند." . "\n \n";
$msgHelp .= "لینک دعوت (/link): با استفاده از این دستور می‌توانید لینک اختصاصی خود را دریافت و برای دوستان خود ارسال کنید و امتیاز دریافت کنید!" . "\n \n";
$msgHelp .= "رتبه‌بندی ربات (/rank): رتبه شما در ربات به تعداد افراد دعوت شده بوسیله شما بستگی دارد و هرچه این افراد بیشتر باشند امتیاز بیشتری به ازای دعوت هر کاربر دریافت می‌کنید." . "\n \n";
$msgHelp .= "راهنمای ربات (/help): مشاهده ویژگی‌ها و دستورات داخل ربات " . "\n \n";
$msgHelp .= "درباره ربات (/about): دریافت لینک کانال مربوط به ربات که در آن جزییات آپدیت‌ها و ... نوشته شده و هر یک ساعت بروزرسانی خودکار می‌شود." . "\n \n";
define("_Msg_Help", $msgHelp);
$msgWelcome = "سلام دوست عزیز 🌺" . "\n";
$msgWelcome .= "به ربات" . " <b>Twitter Box</b> " . "خوش اومدی 😉" . "\n";
$msgWelcome .= $msgHelp;
define("_Msg_Welcome", $msgWelcome);
define("_Msg_UserNotFound", "چنین کاربری در توییتر یافت نشد!");
define("_Msg_Id", "شماره‌ی انحصاری پروفایل");
define("_Msg_Username", "نام کاربری:");
define("_Msg_Name", "نام نمایشی:");
define("_Msg_Bio", "بیوگرافی:");
define("_Msg_Location", "آدرس:");
define("_Msg_Following", "تعداد فالویینگ:");
define("_Msg_Followers", "تعداد فالور: ");
define("_Msg_Listed", "تعداد لیست:");
define("_Msg_Tweets", "تعداد توییت:");
define("_Msg_Verified", "حساب رسمی:");
define("_Msg_Protected", "حساب خصوصی:");
define("_Msg_AllTweet", "لینک توییت‌ها:");
define("_Msg_ValidUsername", "لطفا نام کاربری را صحیح وارد کنید.");
define("_Msg_PrivatePage", "صفحه شما خصوصی(قفل) است، لطفا را عمومی کنید و دوباره تلاش کنید.");
define("_Msg_PleaseWait", "چند لحظه صبر کنید ..");
define("_Msg_Exists", "وجود دارد.");
define("_Msg_IsNoLimit", "محدودیتی ندارد.");
define("_Msg_SearchLimit", "محدودیت جستجو: ");
define("_Msg_Ghostlimit", "محدودیت حالت روح: ");
define("_Msg_Replylimit", "محدودیت پاسخ: ");
define("_Msg_Yes", "بلی");
define("_Msg_No", "خیر");
define("_Msg_SignUp", "ثبت‌نام");
define("_Msg_Days", "روز");
define("_Msg_Hours", "ساعت");
define("_Msg_Minutes", "دقیقه");
define("_Msg_Seconds", "ثانیه");
define("_Msg_Ago", "پیش");
define("_Msg_Time", "ساعت");
define("_Msg_Date", "تاریخ");
define("_Msg_Score", "امتیاز");
define("_Msg_Invited", "دعوت شده");
define("_Msg_YourRank", "رتبه شما");
define("_Msg_DeMedal", "مدال توسعه (🎖)");
define("_Msg_KiMedal", "مدال شاه ربات (👑)");
define("_Msg_GiMedal", "مدال بزرگ ربات (🏅)");
define("_Msg_StMedal", "مدال طلای ربات (🥇)");
define("_Msg_NdMedal", "مدال نقره ربات (🥈)");
define("_Msg_RdMedal", "مدال برنز ربات (🥉)");
$msgLinkDetails = "لینک خود را برای دوستانتان بفرستید و هدیه دریافت کنید 😉" . "\n";
$msgLinkDetails .= "برای مشاهده تعداد افراد ثبت‌نام شده با لینک دعوت شما دستور /profile را وارد کنید، لینک  شما 👇";
define("_Msg_LinkDetails", $msgLinkDetails);
$msgUpdateLan = "زبان بروزرسانی شد!" . "\n";
$msgUpdateLan .= "لطفا کلیک کنید";
define("_Msg_PleaseClick", $msgUpdateLan);
define("_Msg_EmptyScore", "امتیاز کافی جهت انجام این کار را ندارید!");
$msgRank = "رتبه‌بندی های ربات:" . "\n \n";
$msgRank .= "مدال توسعه‌دهنده  (🎖) :" . "\n";
$msgRank .= "- توسعه دهندگان ربات دارای این مدال هستند." . "\n \n";
$msgRank .= "مدال پادشاه ربات (👑) :" . "\n";
$msgRank .= "- دعوت بیش از 8 هزار عضو 🙎‍♂️" . "\n";
$msgRank .= "- دریافت 25% از خرید آن‌ها" . "\n \n";
$msgRank .= "مدال بزرگ ربات (🏅) :" . "\n";
$msgRank .= "- دعوت از 4هزار تا 8هزار عضو 🙎‍♂️" . "\n";
$msgRank .= "- دریافت 20% از خرید آن‌ها" . "\n \n";
$msgRank .= "مدال طلای ربات (🥇) :" . "\n";
$msgRank .= "- دعوت از 2هزار تا 4هزار عضو 🙎‍♂️" . "\n";
$msgRank .= "- دریافت 15% از خرید آن‌ها" . "\n \n";
$msgRank .= "مدال نقره ربات (🥈) :" . "\n";
$msgRank .= "- دعوت از هزار تا 2هزار عضو 🙎‍♂️" . "\n";
$msgRank .= "- دریافت 10% از خرید آن‌ها" . "\n \n";
$msgRank .= "مدال برنز ربات (🥉) :" . "\n";
$msgRank .= "- دعوت از 1 تا هزار عضو 🙎‍♂️" . "\n";
$msgRank .= "- دریافت 5% از خرید آن‌ها" . "\n \n";
define("_Msg_Rank", $msgRank);
define("_Msg_MoreScore", "برای افزایش امتیاز دستور /link را وارد کنید.");
define("_Msg_Link", "لینک: ");
define("_Msg_Favourite", "لایک‌ها: ");
define("_Msg_PrivateUser", "اطلاعات مربوط به این کاربر خصوصی است!");
$msgConnectOne = "سلام دوست عزیز 😎" . "\n";
$msgConnectOne .= "به منوی اتصال حساب توییتر به ربات خوش اومدی، البته خیالت راحت برای کنترل ما نیاز به رمز حسابت نداریم پس به اطلاعات خصوصیت هم دسترسی نداریم 👌🏽" . "\n";
$msgConnectOne .= "این اتصال صرفا جهت حفظ حریم خصوصیت در برابر کاربرای دیگست، به عنوان مثال بعد از اتصال میتونی تعیین کنی فردی دیگه‌ای غیر خودت اطلاعات حسابت رو با استفاده از من بررسی کنه یا نه و ..." . "\n";
$msgConnectOne .= "خب برای شروع کد پروفایل رو کپی کن (یک کلیک روش کنی کپی میشه 😉)  و بعد با استفاده از دکمه‌ی دایرکت من 📨 به صفحه من داخل توییتر برو و کد رو برای من بفرست (دایرکتم برای همه بازه 😬)" . "\n \n";
$msgConnectOne .= "- کد پروفایل: ";
define("_Msg_ConnectOne", $msgConnectOne);
$msgConnectTwo = "بعد از ارسال کد حتما و حتما 180 ثانیه صبر کن و بعدش دکمه‌ی کد را ارسال کردم رو کلیک کن، بعد از اون برات یک سری اطلاعات از حساب توییترت میفرستم، در نهایت پاسخ سوال رو بده و تموم، به همین راحتی، برای اینکه ببینی چه ربات‌ها و برنامه‌هایی به اطلاعات حساب توییترت دسترسی دارند میتونی مسیر زیر رو از طریق برنامه توییتر باز کنی و دسترسیشونو ببندی:" . "\n \n";
$msgConnectTwo .= "<i>Setting and privacy -> Account -> Apps and sessions -> Connected apps -> [Select one] -> Revoke access</i>" . "\n \n";
$msgConnectTwo .= "در ضمن میتونی هر تعداد حساب توییتر که خواستی به این اکانت تلگرامت متصل کنی 🤓" . "\n";
define("_Msg_ConnectTwo", $msgConnectTwo);
define("_Msg_PrivateAccount", "آیا میخواهی اطلاعات توییترت بوسیله همه کاربران ربات قابل نمایش باشه؟");
$msgCurrectlySendCode = "آیا مطمئنی به دایرکت من رمز رو فرستادی؟ 🤔" . "\n";
$msgCurrectlySendCode .= "شاید یک دقیقه صبر نکردی! 🤨" . "\n";
$msgCurrectlySendCode .= "⏳ لطفا یک دقیقه صبر کنید ... ";
define("_Msg_CurrectlySendCode", $msgCurrectlySendCode);
define("_Msg_PrivateInformation", "اطلاعات شما از سایر کاربران مخفی شد.");
define("_Msg_PublicInformation", "اطلاعات شما بوسیله همه در داخل ربات قابل رویت است.");
$msgHiddenInformation = "سلام دوست عزیز 😎" . "\n";
$msgHiddenInformation .= "اطلاعات این حساب توییتری بوسیله ادمین مخفی شده!";
define("_Msg_HiddenInformation", $msgHiddenInformation);
define("_Msg_InjectTwitterProfileOne", "آیا اطمینان به خروج  ");
define("_Msg_InjectTwitterProfileTwo", " از ربات هستید؟\n<i>با خروج شما امکان مشاهده محدودیت شدوبن و ... نمی‌باشید</i>");
define("_Msg_SubmitExitYes", "شما با موفقیت از بات خارج شدید.\nبرای اتصال مجدد دستور /connect را وارد کنید.");
define("_Msg_SubmitExitNo", "خروج از بات کنسل شد!");
define("_Msg_HaveActiveAdmin", "این یوزرنیم دارای ادمین فعال می‌باشد، شما نمی‌توانید به حسابی که ادمین فعال دارد متصل شوید!");
$msgTimePermision = "لطفا بازه زمانی بین هر درخواست خود را رعایت کنید!" . "\n";
$msgTimePermision .= "زمان انتظار: ";
define("_Msg_TimePermision", $msgTimePermision);
define("_Msg_ConnectTwitterProfile", "لطفا ابتدا با دستور /connect حساب توییتری خود را به ربات متصل کنید، سپس دوباره تلاش کنید!");
define("_Msg_GetDataActiveAdmin", "شما نمی‌توانید به اطلاعات کاربری که ادمین فعال دارد دسترسی داشته باشید");
$msgUnfollowerCondition = "شما باید دارای شرایط زیر باشید:" . "\n";
$msgUnfollowerCondition .= "- فالویینگ‌ها  < 5K" . "\n";
$msgUnfollowerCondition .= "- فالورها < 5K" . "\n";
define("_Msg_UnfollowerCondition", $msgUnfollowerCondition);
define("_Msg_Age", "سن توییتری");
define("_Msg_SelectAccount", "لطفا یکی از حساب‌های زیر را انتخاب کنید : 👇🏽");
define("_Msg_Ignore", "کاربر مجاز");
define("_Msg_RepeatPerson", "این فرد قبلا در لیست قرار گرفته!");
define("_Msg_IgnoreMessageOne", "شما کاربر");
define("_Msg_IgnoreMessageTwo", "را در لیست سفید قرار دادید، این به معنی است که استعلام بعدی این فرد در لیست آنفالویاب‌ شما قرار ندارد، برای خارج کردن این فرد دستور زیر را وارد کنید 👇🏽");
define("_Msg_RemoveMessageOne", "شما کاربر");
define("_Msg_RemoveMessageTwo", "را از لیست خارج کردید، برای افزودن مجدد این کاربر دستور زیر را وارد کنید 👇🏽");
define("_Msg_NoUnfollowered", "هیچ فردی شما را آنفالو نکرده!");
define("_Msg_NoWhiteList", "لیست سفید شما خالی است!");
define("_Msg_SelectIgnoredGroup", "لطفا یکی از گروه‌های زیر را انتخاب کنید:");
define("_Msg_NotHaveVideo", "این توییت حاوی ویدیو نمی‌باشد!");
define("_Msg_MovieQuality", "لطفا کیفیت مد نظر را انتخاب کنید:");
define("_Msg_YourVideos", "ویدیوهای شما:");
define("_Msg_PublishedIn", "منتشر شده در");
define("_Msg_DoNotHaveLowQuality", "این ویدیو نسخه کیفیت پایین ندارد!");
define("_Msg_DoNotHaveHighQuality", "این ویدیو نسخه کیفیت بالا ندارد!");
define("_Msg_EmptyVideoList", "لیست ویدیو شما خالی است");
define("_Msg_Repair", "🛠 این فیچر در حال تعمیر است ..");
$msgDeactivate = "سلام و درود 😉" . "\n";
$msgDeactivate .= "متاسفانه، بدلیل ناهماهنگی‌های که بخش سرویس دهی (#TwitterApi) و ربات صورت گرفت، #توییتر تمام دسترسی ما به سرورش رو مسدود کرد، امیدوارم این مشکل بزودی برطرف بشه و بتونیم مثل گذشته با قدرت ادامه بدیم، در صورت داشتن هرگونه سوال و پیشنهاد میتونید بوسیله‌ی آی‌دی زیر با من در ارتباط باشید، در این ربات هیچ گونه تبلیغی صورت نمیگیره، در صورت تمایل میتونید نوتیفیکش این ربات رو فعال کنید تا در صورت فعالیت مجدد اخبار آن به سمع و نظر شما برسه." . "\n \n";
$msgDeactivate .= "ارادتمند شما 🌺" . "\n";
$msgDeactivate .= "محمد زرچی (@ZarchiMohammad)" . "\n";
define("_Msg_Deactivate", $msgDeactivate);
define("_Msg_Renew", "تازه‌سازی");

define("_Btn_Unfollowers", "آنفالویاب");
define("_Btn_Unfollowing", "طرفداران");
define("_Btn_Shadowban", "تست محدودیت شدوبن");
define("_Btn_UnfollowerFind", "آنفالویاب");
define("_Btn_ChackProfile", "استعلام مجدد");
define("_Btn_Languege", "تغییر زبان");
define("_Btn_Link", "لینک دعوت");
define("_Btn_ConnectAccount", "اتصال به توییترباکس");
define("_Btn_ExitFromTwitterAccount", "خروج از ربات");
define("_Btn_HaveActiveAdmin", "ادمین فعال دارد");
define("_Btn_LowQuality", "کیفیت پایین");
define("_Btn_HighQuality", "کیفیت بالا");
