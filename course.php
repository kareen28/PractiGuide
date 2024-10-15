<?php
include 'nav.php';
?>
<html lang="he">
<head>
  <title>האתר שלי</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78Hz0+Bj5HpqVfgrJ8X51t2V6oiz1QPkvc6Bu5qEZYw5uR90KsEyVC4" crossorigin="anonymous">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78Hz0+Bj5HpqVfgrJ8X51t2V6oiz1QPkvc6Bu5qEZYw5uR90KsEyVC4" crossorigin="anonymous">
   <link rel="stylesheet" href="styles.css">
      <style>
  body {
  font-family: Arial, sans-serif;
  background-color: #f2f2f2;
  margin: 0;
  padding: 0;
}

.container {
  max-width: 1200px;
  margin: 20px auto;
  padding: 0 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

h2 {
  text-align: center;
  text-shadow: 8px 8px 8px rgba(0.1, 0.1, 0.1, 0.1); /* צל לטקסט */
 /* צל כדי להבליט את הכותרת */
}

.course-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
}

.course-item {
  width: 250px;
  margin: 10px;
  padding: 10px;
  background-color: #fff;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.course-item p {
  margin-bottom: 5px;
}




.additional-info {
  background-color: #f0f0f0;
  padding: 20px;
  text-align: center;
}

.additional-info h2 {
  font-size: 24px;
  margin-bottom: 10px;
}

.additional-info p {
  font-size: 16px;
}

.additional-info {
  display: flex;
  align-items: center;
}

.circle-container {
  width: 200px;
  height: 200px;
  border-radius: 50%;
  overflow: hidden;
  position: relative;
  float: left;
  margin-right: 20px;
}

.circle-container img {
  object-fit: cover;
}

.text-container {
  float: left;
  width: calc(100% - 240px);
}

.main-content {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  text-align: left;
}

.text {
  width: 60%;
  text-align: right;
  direction: rtl;
  padding: 20px;
}

.circular-image {
  width: 200px;
  height: 200px;
  border-radius: 50%;
  overflow: hidden;
}

.circular-image img {
  width: 100%;
  height: auto;
  display: block;
}

.card-img-top {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.card {
  width: 18rem;
  margin: 10px;
  display: inline-block;
  vertical-align: top;
  background-color: white;
  border-radius: 10px;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}

.card-body {
  text-align: center;
  font-family: Arial, sans-serif;
}

.btn {
  color: white;
  background-color: #007bff;
  border: none;
  border-radius: 5px;
  padding: 8px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;
  font-weight: 300;
}

@media (max-width: 768px) {
  .card {
    width: 100%;
    margin: 10px 0;
  }
}

* {
  margin: 0;
  padding: 0;
}

  </style>
</head>
<body>

  <br>
  <br>

    <center><img src="p6.jpg" alt="תיאור התמונה"></center>
	<br>
   <h2>מסלולים</h2>
   <br>
  
      <div class="container">
        <div class="course-container">
            <?php
            // Establish connection to MySQL database
            $conn = new mysqli("localhost", "root", "", "mydatabase");
            if($conn->connect_error){
                die("Connection failed: " . $conn->connect_error);
            }

            // Retrieve courses from the database
            $courses_result = $conn->query("SELECT * FROM info");
            if ($courses_result->num_rows > 0) {
                // Output data of each row
                while($course_row = $courses_result->fetch_assoc()) {
                    echo '<div class="course-item">';
                    echo '<img src="' . $course_row['image_url'] . '" width="200" /><br>';
                    echo '<p> ' . $course_row['name'] . '</p>';
                    echo '<p> ' . $course_row['description'] . '</p>';
                    echo '</div>';
                }
            } else {
                echo "No courses found.";
            }
            $conn->close();
            ?>
        </div>
    </div>
	
	
	
	<div class="college-container">
  <center><h1>המכללות המוכרות ללמודי הנדסאים</h1></center>
  <center><h3>הנדסאים בצפון</h3></center>
    <div class="card">
      <img src="אורט .jpg" class="card-img-top" alt="מכללת אורט בראודה הנדסאים">
      <div class="card-body">
        <h5 class="card-title">אורט בראודה הנדסאים</h5>
        <p class="card-text">לימודי הנדסאים במכללת אורט בראודה להנדסאים הם לימודים מבוקשים ברמה אקדמית גבוהה, המיועדים להכשיר אנשי מקצוע</p>
        <a href="https://ortcolleges.org.il/%D7%91%D7%99%D7%95%D7%98%D7%9B%D7%A0%D7%95%D7%9C%D7%95%D7%92%D7%99%D7%94/?utm_source=google&utm_medium=cpc&utm_campaign={AWO-%D7%94%D7%A0%D7%93%D7%A1%D7%90%D7%99%D7%91%D7%99%D7%95%D7%98%D7%9B%D7%A0%D7%95%D7%9C%D7%95%D7%92%D7%99%D7%94}&utm_term=%D7%94%D7%A0%D7%93%D7%A1%D7%90%D7%99%20%D7%91%D7%99%D7%95%D7%98%D7%9B%D7%A0%D7%95%D7%9C%D7%95%D7%92%D7%99%D7%94%20%D7%90%D7%95%D7%A8%D7%98%20%D7%91%D7%A8%D7%90%D7%95%D7%93%D7%94&utm_content=673513678505&gad_source=1&gclid=CjwKCAjwtNi0BhA1EiwAWZaANDoVjgK2sc8jkdsTdb9sQ1M3apJlks0hOMGvUdIRy8TKpacmP9kH3BoC-S4QAvD_BwE" class="btn btn-primary">פרטים נוספים</a>
      </div>
    </div>

    <div class="card">
      <img src="נצרת.jpg" class="card-img-top" alt="מכללת נצרת עלית יזרעאל">
      <div class="card-body">
        <h5 class="card-title">מכללת נצרת עלית יזרעאל</h5>
        <p class="card-text">כללת נצרת עילית - יזרעאל פועלת בעיר נצרת עלית מאז שנת 1987 כמכללה טכנולוגית</p>
        <a href="https://www.nitc.co.il/?utm_source=google_ads&utm_medium=cpc&utm_campaign=eng_cpa&gad_source=1&gclid=CjwKCAjwtNi0BhA1EiwAWZaANOV3FLIFXWtT_VeA2pvMX641RGgh3FJjAb9XWFbttuA4XMVSzoZ2vxoChPsQAvD_BwE" class="btn btn-primary">פרטים נוספים</a>
      </div>
    </div>

         <div class="card">
      <img src="טכניון.jpg" class="card-img-top" alt="בית הספר להנדסאים בקריית הטכניון">
      <div class="card-body">
        <h5 class="card-title">בית הספר להנדסאים בקריית הטכניון</h5>
        <p class="card-text">בית הספר הארצי להנדסאים בקריית הטכניון- חיפה, הינו בית ספר וותיק ואיכותי, הפועל מאז שנת 1958 בפיקוח מה"ט.</p>
        <a href="https://www.pet.ac.il/" class="btn btn-primary">פרטים נוספים</a>
      </div>
    </div>
	
	    <div class="card">
      <img src="תל-חי.jpg" class="card-img-top" alt="המכללה הטכנולוגית תל חי">
      <div class="card-body">
        <h5 class="card-title">המכללה הטכנולוגית תל חי </h5>
        <p class="card-text">המכללה הטכנולוגית תל חי הינה מכללה וותיקה (נוסדה ב1957) ובעלת שם.</p>
        <a href="https://www.telhai.tech/" class="btn btn-primary">פרטים נוספים</a>
      </div>
    </div>
	
	    <div class="card">
      <img src="קריתבילק.jpg" class="card-img-top" alt="אורט קריית ביאליק">
      <div class="card-body">
        <h5 class="card-title">אורט קריית ביאליק</h5>
        <p class="card-text">מכללת אורט קרית ביאליק חלק מרשת מכללות אורט ישראל.</p>
        <a href="https://ortcolleges.org.il/%D7%91%D7%99%D7%95%D7%98%D7%9B%D7%A0%D7%95%D7%9C%D7%95%D7%92%D7%99%D7%94/?utm_source=google&utm_medium=cpc&utm_campaign={AWO-%D7%94%D7%A0%D7%93%D7%A1%D7%90%D7%99%D7%91%D7%99%D7%95%D7%98%D7%9B%D7%A0%D7%95%D7%9C%D7%95%D7%92%D7%99%D7%94}&utm_term=%D7%94%D7%A0%D7%93%D7%A1%D7%90%D7%99%20%D7%91%D7%99%D7%95%D7%98%D7%9B%D7%A0%D7%95%D7%9C%D7%95%D7%92%D7%99%D7%94%20%D7%90%D7%95%D7%A8%D7%98%20%D7%91%D7%A8%D7%90%D7%95%D7%93%D7%94&utm_content=673513678505&gad_source=1&gclid=CjwKCAjwtNi0BhA1EiwAWZaANDoVjgK2sc8jkdsTdb9sQ1M3apJlks0hOMGvUdIRy8TKpacmP9kH3BoC-S4QAvD_BwE" class="btn btn-primary">פרטים נוספים</a>
      </div>
    </div>
	
	    <div class="card">
      <img src="רופין.jpg" class="card-img-top" alt="המכללה הטכנולוגית רופין">
      <div class="card-body">
        <h5 class="card-title">המכללה הטכנולוגית רופין</h5>
        <p class="card-text">המכללה הטכנולוגית רופין הינה מהמוסדות הוותיקים.</p>
        <a href="https://mtr.ruppin.tech/" class="btn btn-primary">פרטים נוספים</a>
      </div>
    </div>
	
	    <div class="card">
      <img src="כנרת.jpg" class="card-img-top" alt="מכללת תל אביב">
      <div class="card-body">
        <h5 class="card-title">המכללה האיזורית כנרת</h5>
        <p class="card-text">המכללה הטכנולוגית להנדסאים כנרת בעמק הירדן היא אחת המכללות הוותיקות בתחום</p>
        <a href="https://www.kinneret-minisite.com/?gad_source=1&gclid=CjwKCAjwtNi0BhA1EiwAWZaANA3QtH_D6LmoCzlhpPDagGyJr6vCShZyGnqa3az520TVxwqU8PYiDRoCaSkQAvD_BwE" class="btn btn-primary">פרטים נוספים</a>
      </div>
    </div>
   <br>
   <br>
	  <center><h3>הנדסאים במרכז והסביבה</h3></center>
	<br>
    <br>
	    <div class="card">
      <img src="תל-אביב.jpg" class="card-img-top" alt="הנדסאים תל אביב">
      <div class="card-body">
        <h5 class="card-title">הנדסאים תל אביב</h5>
        <p class="card-text">במוסד לימוד זה, מציעים מגמות לימוד מגוונות ובהן אדריכלות ועיצוב פנים, מכשור רפואי, חשמל, תוכנה, בניין, תעשייה וניהול בהתמחות לוגיסטיקה</p>
        <a href="https://landing.cts.org.il/lp24handesayim/?utm_source=google&utm_term=%D7%94%D7%A0%D7%93%D7%A1%D7%90%D7%99%D7%9D%20%D7%AA%D7%9C%20%D7%90%D7%91%D7%99%D7%91&utm_campaign=21162230616&utm_medium=cpc&utm_content=1008012&gad_source=1&gclid=CjwKCAjwtNi0BhA1EiwAWZaANFBO7-VelK8Qpwgd3GQA4CYfCkMyjp6G1AKiEZUFmMhw0xusumof-RoCtrIQAvD_BwE" class="btn btn-primary">פרטים נוספים</a>  
    </div>
   </div>
   
    <div class="card">
      <img src="מנהל.jpg" class="card-img-top" alt="המכללה למינהל (ראשון לציון) ">
      <div class="card-body">
        <h5 class="card-title">המכללה למינהל (ראשון לציון) </h5>
        <p class="card-text">אזור מרכז הארץ, מפעילה רשת המכללה למינהל שלוחה בעיר ראשון לציון</p>
        <a href="https://www.academy.org.il/?utm_source=google&utm_medium=cpc&utm_content=154182892287&utm_term=%D7%94%D7%9E%D7%9B%D7%9C%D7%9C%D7%94%20%D7%9C%D7%9E%D7%99%D7%A0%D7%94%D7%9C%20%D7%A8%D7%90%D7%A9%D7%95%D7%9F%20%D7%9C%D7%A6%D7%99%D7%95%D7%9F&utm_campaign=search_brand&utm_id=17646779236&gad_source=1&gclid=CjwKCAjwtNi0BhA1EiwAWZaANHd9TZj8MXf_M1KV-hFtWjnp2iQd4DWOg4LlJAxVngTldMvhL5rMgxoCNgEQAvD_BwE" class="btn btn-primary">פרטים נוספים</a>
      </div>
    </div>

     <div class="card">
      <img src="סינ.jpg" class="card-img-top" alt="אורט סינגאלובסקי (תל אביב)">
      <div class="card-body">
        <h5 class="card-title">אורט סינגאלובסקי (תל אביב)</h5>
        <p class="card-text">לימודי הנדסאים במכללת אורט בראודה להנדסאים הם לימודים מבוקשים ברמה אקדמית גבוהה, המיועדים להכשיר אנשי מקצוע</p>
        <a href="https://ortcolleges.org.il/%D7%91%D7%99%D7%95%D7%98%D7%9B%D7%A0%D7%95%D7%9C%D7%95%D7%92%D7%99%D7%94/?utm_source=google&utm_medium=cpc&utm_campaign={AWO-%D7%94%D7%A0%D7%93%D7%A1%D7%90%D7%99%D7%91%D7%99%D7%95%D7%98%D7%9B%D7%A0%D7%95%D7%9C%D7%95%D7%92%D7%99%D7%94}&utm_term=%D7%94%D7%A0%D7%93%D7%A1%D7%90%D7%99%20%D7%91%D7%99%D7%95%D7%98%D7%9B%D7%A0%D7%95%D7%9C%D7%95%D7%92%D7%99%D7%94%20%D7%90%D7%95%D7%A8%D7%98%20%D7%91%D7%A8%D7%90%D7%95%D7%93%D7%94&utm_content=673513678505&gad_source=1&gclid=CjwKCAjwtNi0BhA1EiwAWZaANDoVjgK2sc8jkdsTdb9sQ1M3apJlks0hOMGvUdIRy8TKpacmP9kH3BoC-S4QAvD_BwE" class="btn btn-primary">פרטים נוספים</a>
      </div>
    </div>
	
	    <div class="card">
      <img src="אריאיל.jpg" class="card-img-top" alt="הנדסאים באריאל">
      <div class="card-body">
        <h5 class="card-title">הנדסאים באריאל</h5>
        <p class="card-text">- מתקיימים לימודים בתחומי העיצוב, תעשייה וניהול, תקשורת, ביוטכנולוגיה, בניין ועוד. ניתן ללמוד גם תואר ראשון להנדסאים, מסלול משולב עם תואר ראשון בכלכלה ומנהל עסקים, ועוד.</p>
        <a href="https://handesaim.ariel.ac.il/department/%d7%9e%d7%97%d7%9c%d7%a7%d7%95%d7%aa-%d7%94%d7%a0%d7%93%d7%a1%d7%94-%d7%95%d7%98%d7%9b%d7%a0%d7%95%d7%9c%d7%95%d7%92%d7%99%d7%94/?lm_form=87891&lm_supplier=1196&lm_key=1129de57db&utm_source=google&utm_campaign=alo&utm_medium=cpl&gad_source=1&gclid=CjwKCAjwtNi0BhA1EiwAWZaANDcNYUu4AVpITuKohsVzNCOSR4yFoWi8aVjYcuIHPfTb8X8Gx29svxoCiEoQAvD_BwE" class="btn btn-primary">פרטים נוספים</a>
      </div>
    </div>
	
	    <div class="card">
      <img src="אתגר.jpg" class="card-img-top" alt="אתגר">
      <div class="card-body">
        <h5 class="card-title">אתגר</h5>
        <p class="card-text"> בשלוחת תל אביב של רשת אתגר מתקיימות המגמות חשמל, מכונות, ותעשייה וניהול. נערכים גם לימודי תעודה במקצועות טכנולוגיים.</p>
		<a href="https://landing.etgar.org.il/lpeng24/?ga2=2&cht=1&kwd=%D7%9E%D7%9B%D7%9C%D7%9C%D7%AA%20%D7%90%D7%AA%D7%92%D7%A8&mth=p&crt=692920401274&plc=&dev=c&cid=21085802110&gid=166490619984&fid=&tid=kwd-320148500045&lin=1008006&lph=1008012&ntw=g&dvm=&adp=&channelid=216577&gad_source=1&gclid=CjwKCAjwtNi0BhA1EiwAWZaANOWE-Q_iTxvK1bIPivIOtAQvQi5N3leKiFR6gJRlQSi_rygrVSABvhoCFdgQAvD_BwE" class="btn btn-primary">פרטים נוספים</a>
      </div>
    </div>
	
	    <div class="card">
      <img src="שנקר.jpg" class="card-img-top" alt="שנקר, בית הספר להנדסאים (רמת גן)">
      <div class="card-body">
        <h5 class="card-title">שנקר, בית הספר להנדסאים (רמת גן)</h5>
        <p class="card-text">בית הספר להנדסאים של מכללת שנקר ניתן למצוא מגמות כגון בניין, אדריכלות ועיצוב פנים, תוכנה, אדריכלות נוף, ועיצוב מדיה.</p>
        <a href="https://lp-handesaim.shenkar.ac.il/new-building-engineer-24-2/?utm_source=google&utm_medium=cpc&utm_campaign=%D7%94%D7%A0%D7%93%D7%A1%D7%90%D7%99%20%D7%91%D7%A0%D7%99%D7%99%D7%9F%20|%20Bright%20|%20Serp%20|%20%2002.04&utm_content=A%20study%20building%20engineer&utm_ad=654528107246&utm_term=%D7%94%D7%A0%D7%93%D7%A1%D7%90%D7%99%20%D7%91%D7%A0%D7%99%D7%99%D7%9F%20%D7%9C%D7%99%D7%9E%D7%95%D7%93%D7%99%D7%9D&matchtype=b&device=c&GeoLoc=1008012&placement=&network=g&utm_id=19956424213&campaign_id=19956424213&adset_id=153639811851&ad_id=654528107246&keyword_id=kwd-313354467962&gad_source=1&gclid=CjwKCAjwtNi0BhA1EiwAWZaANJb7TN0MBXRHKwYMoLhxIKDxjY7OsxngzkQEWhZHTAPYR-ksDmPjZxoC8vUQAvD_BwE" class="btn btn-primary">פרטים נוספים</a>
      </div>
    </div>
	  </div>
  </div>
</body>
</html>
