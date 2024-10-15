 <!DOCTYPE html>
<html lang="he"> 
  <head>
  <style>
/* Reset default margin and padding */
* {
    margin: 0;
    padding: 0;
}

/* סגנון כללי */
body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
}

/* סגנון ה-header */
header {
  background-color: #fff;
  height: 80px;
  width: 100%;
}

/* Styling for nav */
nav {
  width: 100%;
  height: 100%;
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  display: flex;
  align-items: center; /* Align items vertically */
}

.logo svg {
  vertical-align: middle; /* Ensure that the SVG icon aligns with the text */
  margin-right: 15px; /* */
}

.logo a {
  text-decoration: none;
  color: #000;
}

/* סגנון ה-ul */
ul {
  margin: 0;
  padding: 0;
  list-style: none;
  display: flex;
  align-items: center;
}

li {
  margin: 0 15px;
}

li a {
  text-decoration: none;
  color: #000;
  font-size: 16px;
  font-weight: 500;
  transition: color 0.3s;
}

li a:hover {
  color: #888;
  background-color: #ccc;
  padding: 8px 15px;
  border-radius: 5px;
}

/* סגנון תגובה למכשירים ניידים */
@media (max-width: 768px) {
  nav {
    flex-direction: column;
  }

  ul {
    flex-direction: column;
    text-align: center;
  }

  li {
    margin: 10px 0;
  }
}

 
</style>
</head>
<body>
  <header>
  <nav>
    <div class="logo">
      <a href="profile.php">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="50" fill="currentColor" class="bi bi-person-gear" viewBox="0 0 16 16">
          <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m.256 7a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1zm3.63-4.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0"/>
        </svg>
      </a>
      <a href="index.php">
        <img src="p1.jpg" alt="לוגו" style="width: 120px; height: 70px; margin-left:20px;">
      </a>
    </div>
    <ul>
      <li><a href="index.php">דף הבית</a></li>
      <li><a href="course.php">מסלולים</a></li>
      <li><a href="mahat.php">מה"ט</a></li>
      <li><a href="mainstudentwork.php">סטודנטים עובדים</a></li>
      <li><a href="ChatHelper">CHAT HELPER</a></li>
      <li></li>
      <li>
        <a href="login.php">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
          </svg>
        </a>
      </li>
      <li></li>
      <li>
        <a href="survey.php">
          <img src="p7" style="width:30px;height:30px;">
        </a>
      </li>
    </ul>
  </nav>
</header>
  </body>