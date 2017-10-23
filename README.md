# ieeeday17-server
Código del servidor para la Gymkhana de [ieeeday17-app](https://github.com/IEEEsbUS/ieeeday17-app)

Para utilizar este código es necesario mysql y php.

Nuestro código se divide en 7 ficheros. Los ficheros actualizar.php, iniciar.php, ranking.php y respuesta.php son los que se utilizarán para realizar peticiones desde nuestra APP. La respuesta a una petición siempre se da en formato JSON. La petición se hace utilizando el método GET

- mysql_login.php contiene las constantes para realizar la conexión a la base de datos
- Database.php contiene las funciones para conectarse a la base de datos, utiliza el fichero mysql_login.php
- Meta.php contiene las funciones para procesar las peticiones que nos llegan desde la APP

- actualizar.php se utiliza tanto para registrarse por primera vez en un equipo como para actualizar el estado de las pruebas
- iniciar.php se utiliza para comenzar una prueba, recibe unos parámetros y devuelve, si le corresponde, la descripción de esa prueba
- respuesta.php recibe la solución a una prueba y devuelve si la solución es correcta o no
- ranking.php devuelve un JSON con todos los equipos, las pruebas que han resuelto y el tiempo que llevan acumulado


# Tablas mysql
Nuestro servidor utilizará 4 tablas

MISIONES:<br/>
create table misiones (n_mision VARCHAR(2), descripcion VARCHAR(500),solucion VARCHAR(20));<br/>
<pre>+-------------+--------------+------+-----+---------+-------+
| Field       | Type         | Null | Key | Default | Extra |
+-------------+--------------+------+-----+---------+-------+
| n_mision    | varchar(2)   | YES  |     | NULL    |       |
| descripcion | varchar(500) | YES  |     | NULL    |       |
| solucion    | varchar(20)  | YES  |     | NULL    |       |
+-------------+--------------+------+-----+---------+-------+<br/></pre>

RESPUESTAS:<br/>
create table respuestas (hora BIGINT, equipo VARCHAR(50), password VARCHAR(50),n_mision VARCHAR(2),respuesta VARCHAR(50),correcto VARCHAR(1));
<pre>+-----------+-------------+------+-----+---------+-------+
| Field     | Type        | Null | Key | Default | Extra |
+-----------+-------------+------+-----+---------+-------+
| hora      | bigint(20)  | YES  |     | NULL    |       |
| equipo    | varchar(50) | YES  |     | NULL    |       |
| password  | varchar(50) | YES  |     | NULL    |       |
| n_mision  | varchar(2)  | YES  |     | NULL    |       |
| respuesta | varchar(50) | YES  |     | NULL    |       |
| correcto  | varchar(1)  | YES  |     | NULL    |       |
+-----------+-------------+------+-----+---------+-------+</pre>

EQUIPOS:<br/>
create table equipos(equipo VARCHAR(50),password VARCHAR(50),m_actual VARCHAR(2),m_inicial VARCHAR(2));
<pre>+-----------+-------------+------+-----+---------+-------+
| Field     | Type        | Null | Key | Default | Extra |
+-----------+-------------+------+-----+---------+-------+
| equipo    | varchar(50) | YES  |     | NULL    |       |
| password  | varchar(50) | YES  |     | NULL    |       |
| m_actual  | varchar(2)  | YES  |     | NULL    |       |
| m_inicial | varchar(2)  | YES  |     | NULL    |       |
+-----------+-------------+------+-----+---------+-------+</pre>

CLASIFICACIÓN:<br/>
create table clasificacion (equipo VARCHAR(50),tiempo BIGINT,n_resueltas VARCHAR(2));
<pre>+-------------+-------------+------+-----+---------+-------+
| Field       | Type        | Null | Key | Default | Extra |
+-------------+-------------+------+-----+---------+-------+
| equipo      | varchar(50) | YES  |     | NULL    |       |
| tiempo      | bigint(20)  | YES  |     | NULL    |       |
| n_resueltas | varchar(2)  | YES  |     | NULL    |       |
+-------------+-------------+------+-----+---------+-------+</pre>


La tabla misiones contiene el número de la prueba, la descripción y la respuesta. Esta tabla no se modifica desde el código php.

La tabla respuestas contiene todas las respuestas que ha dado cada equipo a cada prueba junto con la hora en la que lo hicieron y si es correcta o no.

La tabla equipos contiene a todos los equipos, su contraseña y la misión actual e inicial.

La tabla clasificación contiene el nombre del equipo, el tiempo acumulado que llevan y el número de pruebas superadas. Esta tabla se actualiza cada vez que un equipo resuelve una prueba.


La tabla misiones debe quedar fijada antes de comenzar la gymkhana.<br/>
La tabla respuestas debe estar vacía al comienzo de la gymkhana.<br/>
La tabla equipos debe contener el nombre y contraseña de cada equipo. Antes de comenzar la gymkhana, m_actual y m_inicial deben tener el mismo valor.<br/>
La tabla clasificación debe contener todos los nombres de los equipos y los campos tiempo y n_resueltas con el valor 0 antes del comienzo de la gymkhana.
