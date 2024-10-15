<?php 
include 'nav.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$show_modal = isset($_SESSION['show_modal']) && $_SESSION['show_modal'];
unset($_SESSION['show_modal']);
?> 
<!DOCTYPE html>
<html lang="he">
<head>
  <title>האתר שלי</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78Hz0+Bj5HpqVfgrJ8X51t2V6oiz1QPkvc6Bu5qEZYw5uR90KsEyVC4" crossorigin="anonymous">

  <link rel="stylesheet" href="styles.css">
   <style>
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 50%; /* Could be more or less, depending on screen size */
            text-align: center;
        }

        button {
            margin: 10px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>


<style>
/עצוב המלים/
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f3f3f3;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            direction: rtl;
            text-align: justify;
            line-height: 1.6;
        }

            .container p {
                margin: 0 0 20px;
            }

                .container p:first-of-type:first-letter {
                    font-size: 36px;
                    color: #333;
                    float: right;
                    margin-left: 5px;
                    line-height: 1;
                }

                .container p:first-of-type {
                    font-size: 18px;
                    font-weight: bold;
                    color: #333;
                }

            .container p {
                font-size: 16px;
                color: #666;
            }
    </style>
<style>
.btn {
  font-size: 1.2rem;
  padding: 1rem 2.5rem;
      left: 20px; /* מרחק מהצד */

  border: none;
  outline: none;
  border-radius: 0.4rem;
  cursor: pointer;
  text-transform: uppercase;
  background-color: rgb(14, 14, 26);
  color: rgb(234, 234, 234);
  font-weight: 700;
  transition: 0.6s;
    position: fixed; /* קביעת המיקום כקבוע */
            bottom: 50px; /* מרחק מהתחתית */
  box-shadow: 0px 0px 60px #1f4c65;
  -webkit-box-reflect: below 10px linear-gradient(to bottom, rgba(0,0,0,0.0), rgba(0,0,0,0.4));
}

.btn:active {
  scale: 0.92;
}

.btn:hover {
  background: rgb(2,29,78);
  background: linear-gradient(270deg, rgba(2, 29, 78, 0.681) 0%, rgba(31, 215, 232, 0.873) 60%);
  color: rgb(4, 4, 38);
}
</style>
<style>
footer {
    background-color: #000;
    color: #fff;
    padding: 10px;
}

.footer-content {
    text-align: center;
}

.footer-content h2 {
    margin-bottom: 10px;
}


</style>
<style>
/מי אנחנו/
        .flip-card {
            background-color: transparent;
            width: 100%;
            height: 100%;
            perspective: 1000px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .flip-card-inner {
            position: relative;
            width: 250px;
            height: 280px;
            text-align: center;
            transition: transform 0.6s;
            transform-style: preserve-3d;
			
        }

        .flip-card-front, .flip-card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            padding: 5px;
            border-radius: 2em;
            backface-visibility: hidden;
			    background-color: lightblue; /* שינוי הצבע ל-LIGHTBLUE */
    border: 4px solid #ADD8E6;
        }

        .flip-card-front {
           background-color: lightblue; /* שינוי הצבע ל-LIGHTBLUE */
    border: 4px solid #ADD8E6;
        }

        .profile-image {
            background-color: transparent;
            border: none;
            margin-top: 15px;
            border-radius: 5em;
            width: 120px;
            height: 120px;
            margin-left: 50px;
        }

        .pfp {
            border-radius: 35em;
            fill: #c143da;
        }

        .name {
            margin-top: 15px;
            font-size: 27px;
            color:#FFFFFF;
            font-weight: bold;
        }

        .flip-card-back {
              background-color: lightblue; /* שינוי הצבע ל-LIGHTBLUE */
    border: 4px solid #ADD8E6;
    transform: rotateY(180deg);
    padding: 11px;
        }

        .description {
            margin-top: 10px;
            font-size: 14px;
            letter-spacing: 1px;
            color: white
        }

        .socialbar {
            background-color: transparent;
            border-radius: 3em;
            width: 90%;
            padding: 14px;
            margin-top: 11px;
            margin-left: 10px;
            word-spacing: 24px;
            color: white;
        }

            .socialbar svg {
                transition: 0.4s;
                width: 20px;
                height: 20px;
                color: #ADD8E6
            }

                .socialbar svg:hover {
                    color: white;
                }

        .flip-card:hover .flip-card-inner {
            transform: rotateY(180deg);
        }

        .bi-emoji-smile {
            width: 80px;
            height: 80px;
        }

        .bi-emoji-smile {
            fill: white;
        }
        .flip-card-container {
            display: flex;
            justify-content: center; /* ממריץ את הכרטיסיות להיות באמצע */
            gap: 58px; /* מרווח בין הכרטיסיות */
        }



    </style>
	<style>
	#grad1 {
    height: auto;
    background-color: red; /* לדפדפנים שאינם תומכים בגרדיאנטים */
    background-image: linear-gradient(to left, lightblue, white);
    display: flex;
    align-items: flex-start;
    padding: 10px; /* מרווח פנימי */
}

#grad1 img {
    margin-left: 20px; /* מרווח בין התמונה לטקסט */
    width: 150px; /* רוחב התמונה */
    height: auto; /* שמירה על יחס הגובה-רוחב של התמונה */
    border-radius: 10px; /* פינות מעוגלות לתמונה */
}

#grad1 p {
    margin: 0; /* ביטול מרווח הפסקאות */
    max-width: 50%; /* להגבלת רוחב הטקסט */

}
.footer-image {
    width: 100%;
    margin-top: 10px;
    border-radius: 10px; /* פינות מעוגלות לתמונה התחתונה */
}
	</style>
	<style>
	.carousel {
            width: 100%;
            max-width: 100%;
            overflow: hidden;
            position: relative;
        }

        .carousel-item {
            position: relative;
            display: none;
        }

        .carousel-item.active {
            display: block;
        }

        .carousel img {
            width: 100%;
            height: 600px; /* Increased height for the carousel images */
            object-fit: cover;
        }

.carousel-text {
        position: absolute;
        top: 50%;
        left: 10%; /* Move the text slightly to the left */
        transform: translateY(-50%);
        color: white;
        font-family: 'Montserrat', sans-serif; /* Change the font to something more elegant */
        text-align: left; /* Align the text to the left */
        padding: 20px; /* Add padding to the text */
       
        background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
    }

    .carousel-text h2 {
        margin: 0;
        font-size: 30px; /* Adjust the font size */
        line-height: 1.2; /* Adjust line height for better readability */
    }

    .carousel-text p {
        margin: 10px 0; /* Add some margin to separate paragraphs */
        font-size: 20px; /* Adjust the font size */
        line-height: 1.5; /* Adjust line height for better readability */
    }

        .carousel-buttons {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            transform: translateY(-50%);
        }

        .carousel-button {
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }
	</style>

</head>

<body>

<?php if ($show_modal): ?>
<div id="feedbackModal" class="modal">
    <div class="modal-content">
        <p>האם אתה מרוצה מהשירות?</p>
        <button onclick="submitFeedback('yes')">כן</button>
        <button onclick="submitFeedback('no')">לא</button>
    </div>
</div>
<script>
function submitFeedback(answer) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "submit_feedback.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById("feedbackModal").style.display = "none";
        }
    };
    xhr.send("feedback=" + answer);
}

window.onload = function() {
    document.getElementById("feedbackModal").style.display = "block";
};
</script>
<?php endif; ?>

<section class="additional-info">
 <div class="carousel">
        <div class="carousel-item active">
            <img src="p8.jpg" alt="Slide 1">
            <div class="carousel-text">
               <h2>Find your passion</h2>
                <p>while living life to the fullest</p>
            </div>
        </div>
		
        <div class="carousel-item">
            <img src="p9.jpg" alt="Slide 2">
            <div class="carousel-text">
                <h2>Learn interesting things</h2>
                <p>Dream big and dare to fail</p>
            </div>
        </div>
		
        <div class="carousel-item">
            <img src="p10.jpg" alt="Slide 3">
            <div class="carousel-text">
                <h2>just do it</h2>
                <p>Learning never exhausts the mind</p>
            </div>	
        </div>
		
		   <div class="carousel-item">
            <img src="p13.jpg" alt="Slide 3">
            <div class="carousel-text">
                
                <p>The expert in anything was once a beginner</p>
            </div>
        </div>
		
		  <div class="carousel-item">
            <img src="p11.jpg" alt="Slide 3">
            <div class="carousel-text">
                <h2>Your start starts from here</h2>
                <p>Strive for progress, not perfectionn</p>
            </div>
        </div>
		
		  <div class="carousel-item">
            <img src="p12.jpg" alt="Slide 3">
            <div class="carousel-text">
              
                <p>Believe you can and you're halfway there</p>
            </div>
        </div>
		
        <div class="carousel-buttons">
            <button class="carousel-button prev">‹</button>
            <button class="carousel-button next">›</button>
        </div>
    </div>
<script>
        const slides = document.querySelectorAll('.carousel-item');
        const prevButton = document.querySelector('.carousel-button.prev');
        const nextButton = document.querySelector('.carousel-button.next');
        let currentSlide = 0;

        function showSlide(index) {
            slides[currentSlide].classList.remove('active');
            currentSlide = (index + slides.length) % slides.length;
            slides[currentSlide].classList.add('active');
        }

        prevButton.addEventListener('click', () => {
            showSlide(currentSlide - 1);
        });

        nextButton.addEventListener('click', () => {
            showSlide(currentSlide + 1);
        });

        setInterval(() => {
            showSlide(currentSlide + 1);
        }, 5000); // Change slide every 5 seconds
    </script>
</section>

  <main class="main-content">
 

 <div class="container" id="grad1">
       
	        <img src="p14" alt="תיאור תמונה">

        <p> ההבדל בין הנדס והנדסאים ?</p>
        <p>
            הנדסה והנדסאיה הם שני תחומים שונים, גם במשך הלימודים וגם ביישומם המקצועי. לימודי הנדסה לתואר ראשון ארוכים יותר ומעניקים תואר אקדמי, בעוד שלימודי הנדסאיה נמשכים רק שנתיים ונחשבים לקורס מקצועי בלבד. הנדסאים נהנים מיוקרת גבוהה יותר ויכולת עבודה מהירה, אך תואר הנדסאי לא מעניק קרדיט אקדמי. התואר הנדסי מוכר על ידי המכון הישראלי לניהול טכנולוגי
        </p>
    </div>

    <div class="container" id="grad1"style="width: 80%;">

        <p>
            מה ההבדלים בין מסלולי הלימוד להנדסאי ולמהנדס והאם ינתין להמשיך למסלול הנדסה ?
        </p>

		
        <p> מספר סדנאות אקדמיות מציעות תוכניות השלמה לתואר בהנדסה, מאפשרות למהנדסים מוכשרים בתחומים שונים להשתפר ביכולותיהם ולקבל תואר בהנדסה. על מנת להיכנס לתכנית, נדרשים מועמדים מצטיינים, עם דרישות קבלה מאתגרות המתבססות על תנאים כגון ציון מתאם מעל ממוצע  ובבחינת קבלה פסיכומטרית. משך הלימודים בתוכניות אלו יכול להשתנות בהתאם לכללי המוסד, אך לרוב, הלימודים מורכבים ונמשכים שלוש שנים. השלמה זו מאפשרת למועמדים לשפר את הידע המקצועי שלהם ולקדם לתפקידים מובילים יותר במסגרת</p>

    </div>
    

    <div class="container" id="grad1">
        <p>
            סוג התעודה שמקבלים
        <p> הנדסאים מקבלים בסיום לימודיהם דיפלומה ותעודת גמר מטעם המכללה בה למדו.

</div>


    <div style="height: 1500px; background-color: red;background-image: linear-gradient(to right, lightblue, white); " class="container" >
        <p>
            המסלולים המבוקשים ביותר :
        </p>
		<img src="p15.jpg" alt="תמונה תחתונה" class="footer-image">
        <h2>הנדסאי תוכנה :</h2>
        <p>
        השכר הגבוה ביותר מבין ההנדסאים
        מסלול הנדסאי תוכנה הוא אחד המסלולים המבוקשים והמובהקים ביותר בישראל. בנוסף לתכנית הלימודים המקיפה והמגוונת, הוא מייצג אופציה משתלמת וכדאית ביותר, בעיקר כאשר נכנסים לחשבון את היתרונות הבאים:
        שכר גבוה: הנדסאיי תוכנה מרוויחים בדרך כלל את השכר הגבוה ביותר במשק. השכר הגבוה משמעותי במיוחד ככל שהם רוכשים ניסיון נוסף.
        תנאים נוחים: תוך כדי עבודה נדיבה, הנדסאיי תוכנה נהנים מתנאים נוחים ומפנקים ומסטטוס חברתי משודרג.
        סיום מהיר הנדסאיי תוכנה יכולים להשלים את תואר הנדסה תוך תקופה קצרה של עד שלוש שנים, מה שמעניק להם תואר מוערך בזמן קצר.
       
           <h2> הנדסאי אדריכלות ועיצוב פנים:</h2>
        <p>
    הוא מסלול ייחודי המשלב בין אדריכלות ועיצוב פנים, מעניק יכולות מגוונות ומאפשר קריירה בתחום העיצוב. יתרונותיו כוללים התנסות במגוון רחב של תפקידים, יכולת להרוויח שכר גבוה, והזדמנויות עבודה במגזרים הפרטי והציבורי. הלימודים מכינים את הסטודנטים לעבודה בתחום ומאפשרים להם לקבל זכות חתימה לבניינים עד לגובה של 4 קומות. ההתמקדות הכפולה באדריכלות ובעיצוב פנים מקלה על הסטודנטים למצוא עבודה ומאפשרת להם להשלים בין חשיבה הנדסית וחשיבה אומנותית.
</p>
           <h2> הנדסאי אדריכלות נוף – מקצוע שנמצא בצמיחה:</h2>
			<p>
        הנדסאי אדריכלות נוף הוא תחום חדש יחסית שבצמיחה מרשימה, וזאת בהתבסס על דרישה גבוהה לאיכות הסביבה, ידידותיות לסביבה ואיזון מרחבי. עם זאת, גם כשמדובר בסך הכל במסלול של שנתיים, יתרונות רבים מאוד הופכים אותו למבוקש:
        קריירה מגוונת: הנדסאי אדריכלות נוף ניתן להשתלב במגוון רחב של ענפי עבודה, מה שמונע שעמומות או שחיקה בתפקידו.
        חשיבות לסביבה: התמקדות זו בחשיבות הסביבתית והפיתוח הסביבתי מקנה למקצוע זה משמעות אמיתית ומעניינת למי שמעוניין בקריירה משמעותית.
        ביקוש גבוה: עם התפתחות התחום הסביבתי, גם יוקרת המקצוע והביקוש להנדסאי אדריכלות נוף מתרבים.
        </p>
   
                                                     <h2>
                                                         הנדסאי בניין :
                                                     </h2>
                                                         <p>להיכנס בתנופה לתחום שנמצא בתנופה מתמדת
                                                         כאשר מדברים על הנדסאי המבוקש ביותר, אי אפשר להתעלם מהנדסאי בניין. זהו מקצוע מכובד ונחשב במיוחד במשק הישראלי וברחבי העולם. הנדסאי בניין נחשב למקצוע מבוקש מאוד וגם מרוויח ומכובד. ישראל נמצאת בתנופת בנייה שאינה צפויה להיפסק בעתיד הקרוב, ולכן הביקוש להנדסאים בניין נמשך. כל אלו היתרונות שהופכים את מסלול הנדסאי בניין למבוקש:
                                                         שכר משתלם: הנדסאי בניין מרוויח שכר גבוה מהממוצע, והשכר שלו עולה עם ניסיון העבודה. הנדסאי בניין מנוסה יכול להרוויח עד פי 2 מהנדס בניין ללא ניסיון.
                                                         לימודים מעניינים: הלימודים משלבים לימודים תיאורטיים-עיוניים עם פעילויות מעשיות כמו שרטוט, סדנאות וסיורי העשרה באתרים, מה שמספק חוויה ריאלית ומעשית.
                                                         מגוון תפקידים: הנדסאי בניין יכול לעבוד במגזר הפרטי והציבורי, עם יזמים, קבלנים, משרדי תכנון ובנייה, מפעלים, רשויות מקומיות ומחוזיות ועוד.
                                                         אפשרויות קידום: התחום מציע אפשרויות קידום רבות למי שמציג מומחיות ומיומנות.
                                                       
                                                       </p>  <h2>הנדסאי תקשורת חזותית (עיצוב מדיה):</h2>
        <p>
                                                         הוא מסלול צבעוני ומעודכן המאפשר לבוגרים להתפתח במגוון תפקידים בחברות טכנולוגיה, אינטרנט, פיתוח אתרים, דפוס, שיווק ופרסום, אנימציה, צילום ועריכה, עיצוב גרפי, מיתוג ועוד. היתרונות שהופכים את מסלול הנדסאי תקשורת חזותית (עיצוב מדיה) למבוקש הם: עבודה מגוונת, אפשרות להתמקצע בתחומים ספציפיים, לימודים צבעוניים ומעשיים, שכר גבוה, ביקוש גובר ואפשרות לעבוד כשכיר או עצמאי.
                                                        
                                                         לבסןף לפי מחקרים שונים יכולים להגיד ש התחום המבוקש ביותר הוא הנדסאי בניין, שמרוויח כ-14,000 ₪ ברוטו לחודש + רכב וטלפון לאחר שנתיים ניסיון בלבד.
                                                        
                                                     </p>
                                                         בכן, ביקוש למסלולי הנדסאים גבוה כיום והמקצוע זוכה לכבוד רב. המעסיקים מחפשים מועמדים עם הידע והכישורים הנדרשים, וככל שהביקוש עולה, גם היוקרה של המשרות מתרגמת לשכר גבוה ותנאים משמעותיים יותר. במקביל, רבים מתעניינים בלימודים פרקטיים ומסלולי לימוד שאפשר להשלים בזמן ניכר, מאחר שרוצים להיכנס לשוק העבודה במהרה וביטחון<p>
                                                     </p>
    </div>
		<button class="btn" onclick="redirectToAlert()">
פנה אלינו 
</button>
 <script>
    function redirectToAlert() {
        // עבור לדף ההתראה
        window.location.href = "turnedtous.php";
    }
</script>
	</main>
	
    <center><h1>about us</h1></center>

<div class="flip-card-container">
	<div class="flip-card-container">

      <div class="flip-card">
          <div class="flip-card-inner">
              <div class="flip-card-front">
                  <div class="profile-image">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-smile" viewBox="0 0 16 16">
                          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                          <path d="M4.285 9.567a.5.5 0 0 1 .683.183A3.5 3.5 0 0 0 8 11.5a3.5 3.5 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683M7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5m4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5" />
                      </svg>
                      <div class="name">
                          kareen barkat
                      </div>
                  </div>
              </div>
              <div class="flip-card-back">
                  <div class="Description">
                      <p class="description">
                          Hello, i am a practical software engineer studying at Ort Bruade with good experience in web development.
                          I specialize in building scalable, high-profermance we applications using modern webtechnologies.
                      </p>
                  </div>
              </div>
          </div>
      </div>



      <div class="flip-card">
          <div class="flip-card-inner">
              <div class="flip-card-front">
                  <div class="profile-image">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-smile" viewBox="0 0 16 16">
                          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                          <path d="M4.285 9.567a.5.5 0 0 1 .683.183A3.5 3.5 0 0 0 8 11.5a3.5 3.5 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683M7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5m4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5" />
                      </svg>
                      <div class="name">
                          amal abu deab
                      </div>
                  </div>
              </div>
              <div class="flip-card-back">
                  <div class="Description">
                      <p class="description">
                          Hello, i am a practical software engineer studying at Ort Bruade with good experience in web development.
                          I specialize in building scalable, high-profermance we applications using modern webtechnologies.
                      </p>
                  </div>
              </div>
          </div>
      </div>
      </div>
	        </div>




<br>
<br>
<br>
<br>

<footer>
    <div class="footer-content">
	כל הזכויות שמורות 2024

<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-c-circle" viewBox="0 0 16 16">
  <path d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.146 4.992c-1.212 0-1.927.92-1.927 2.502v1.06c0 1.571.703 2.462 1.927 2.462.979 0 1.641-.586 1.729-1.418h1.295v.093c-.1 1.448-1.354 2.467-3.03 2.467-2.091 0-3.269-1.336-3.269-3.603V7.482c0-2.261 1.201-3.638 3.27-3.638 1.681 0 2.935 1.054 3.029 2.572v.088H9.875c-.088-.879-.768-1.512-1.729-1.512"/>
</svg>
    </div>
  
</footer>
</body>
</html>