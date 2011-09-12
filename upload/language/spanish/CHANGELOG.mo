<?php

$lang['Version']     = 'Versión';
$lang['Description'] = 'Descripción';
$lang['changelog']   = array(

'2.9.6mu patch4' => ' 15/11/2010

- [fix] Lock table in class.FlyingFleetHandler.php(death).-
- [fix] Alliance page(NeuruS).-
- [fix] Acs(jtsamper).-
- [fix] ShowPhalanxPage.php adapted to multiuniverse(jstar).-
- [fix] Phalanx rate(lordz & slaver7).-
- [fix] Phalanx messagge for all languages(lordz).-
- [fix] Time in spy report(Think).-

- [add] French language(WhyNot).-
- [add] Turkish language(torres_).-
- [add] Russian language(?).-
',

'2.9.6mu patch3' => ' 13/11/2010

- [fix] Flotten.js(jstar).-
- [fix] All building queue bug(jstar).-
- [fix] Maintaining position V2(jstar).-
',

'2.9.6mu patch2' => ' 18/09/2010

- [fix] Missiles(jstar).-
',

'2.9.6mu patch' => ' 13/09/2010

- [fix] Flotten.js--> adapted to multiuniverse(jstar).-
- [fix] ShowshipyardPage-->hangar time(jstar).-
- [fix] GeneralFunction-->distance()(jstar).-

- [Novità] Italian and english languages(jstar).-
- [Novità] Anticheater in reg.php(jstar).-

- [Cambio] Best countdown(jstar).-
',

'2.9.6mu' => ' 31/08/2010

- [Novità] Inizio del progetto(jstar).-
- [Novità] Struttura database cambiata per gestire gli universi(jstar).-
- [Novità] Riscrittura di tutte le query(jstar).-
- [Novità] Registrazione con nome universo(jstar).-
- [Novità] Countdown in riepilogo per l\'inizio e fine della guerra(jstar).-
- [Novità] Template estesi per gestire le nuove caratteristiche dinamiche(jstar).-
- [Novità] Pannello admin per gestire universi(jstar).-
- [Novità] Pannello admin per gestire guerre(jstar).-
',

'2.9.6' => ' 00/00/10

- [Seguridad] Validaciones globales para prevenir SQL Injections.-
- [Seguridad] Cerrados varios agujeros de seguridad que permitian SQL Injection.-
- [Seguridad] Corregido un bug de seguridad en los bbcode de la alianza (By slave7).-
- [Seguridad] Corregidos varios bugs de seguridad en las páginas de la alianza y opciones.-

- [Fixs]
#0009: Alianza Contador de caracteres
#0010: Contador de caracteres mensaje
#0011: Texto de la solicitud de alianza
#0013: Cancelar construcciones
#0014: Problemas con nuevas versiones
#0015: Link roto en actualización
#4485: El Administrador desaparece por inactivo
#4534: Fuego rápido no se calcula
#4535: Mantener posicion
#4536: Problemas con el phalanx
#4537: Error en el lenguaje
#4538: Bug en misión de expedicion
#4539: Pequeño bug visual en vista de mensajes
#4540: Saltos en la galaxia
#4558: Bug de seguridad
#4719: Problemas nanobots
',

'2.9.5' => ' 29/07/10

- [Seguridad] Corregidos varios bugs de seguridad (By slave7).-
- [Seguridad] Corregidos varios bugs de seguridad (By jtsamper).-

- [Cambio] Adaptado para que sea compatible con PHP 5.3.3.-
- [Cambio] Aparecen todas las personas que aportaron algo en los créditos del panel administrativo.-


- Vieja numeración
- [Fix][Bug #5] Los sacs no coordinan los tiempos (By slave7).-

- Numeración del viejo Bug Tracker (http://sourceforge.net/apps/mantisbt/xgproyect/view_all_bug_page.php)
- [Fix][Bug #0000011] Los Sacs reparten los recursos (By slave7).-
- [Fix][Bug #0000065] Todas las rondas aparecen como ganadas y a eventualmente empatan (By Nickolay).-
- [Fix][Bug #-------] La planta de fusión funcionaba al 100% cuando el planeta no tenía deuterio (By slaver7)

- Nueva numeración (Arctic Tracker)
- [Fix][Bug #0000002] Corregido el link que da más información sobre la protección de novatos.-
- [Fix][Bug #0000003] Corregido el bug en la alianza que causaba problemas para redirigir al sitio de la alianza.-
- [Fix][Bug #0000004] Bug en la producción, produce más de lo que debe (By Calzon).-
- [Fix][Bug #0000005] Bug en el incremento de puntos de las tecnologías (By Think).-
- [Fix][Bug #0000006] Bug en el Debug Log (By Green).-

',

'2.9.4' => ' 02/03/10
- [Novedad] Ahora el mercader requiere materia oscura (2500), configurable desde constants.php.-

- [Seguridad] Limitado desde la base de datos la cantidad de cupulas que pueden ser contruidas (Gracias a medel).-
- [Seguridad] Mejora de seguridad en el formulario de los misiles.-

- [Fix][Bug #0000031] Misión expedición da escuadron fantasma.-
- [Fix][Bug #0000035] Se pueden enviar misiles interplanetarios a usuarios en vacaciones (By Neurus).- 
- [Fix][Bug #0000036] Se pueden enviar misiles interplanetarios en negativo.-
- [Fix][Bug #0000037] Se puede atacar con misiles interplanetarios a usuarios fuertes y debiles (By Neurus).-
- [Fix][Bug #0000038] Luego de un error o advertencia en el envio de misiles no se vuelve a galaxia.-
- [Fix][Bug #0000039] Al colonizar con recursos estos desaparecen.-
- [Fix][Bug #0000043] En INGAME.mo hay 2 entradas iguales.-
- [Fix][Bug #0000044] Varias cupulas de protección (By Neko).-
- [Fix][Bug #0000047] Error en template estadisticas.-
- [Fix][Bug #0000048] Recursos negativos.-
- [Fix][Bug #0000050] Ataques con sondas de espionaje.-
- [Fix][Bug #0000056] Error al editar usuario en el panel administrativo (By Neko).-
- [Fix][Bug #0000059] Solucionado el error que mostraba registros dobles en las estadísticas.-
- [Fix][Bug #-------] Fallo producido al recargar la página de los edificios, defensas, tecnologias y naves (By Neko).-
- [Fix][Bug #-------] Corregido un bug que no instalaba la tabla para los plugins.-
- [Fix][Bug #-------] Corregido un bug que no tenía en cuenta a la supernova en las expediciones.-
- [Fix][Bug #-------] Corregido un bug que ocurria por tener menos deuterio que combustible a gastar (By Neko).-
- [Fix][Bug #-------] No se mostraba correctamente el mensaje de modo vacaciones (By Neko).-
- [Fix][Bug #-------] Si la cuenta está en modo borrar y en modo vacaciones, en el overview se le da prioridad al mensaje de borrado de la cuenta.-
- [Fix][Bug #-------] Corregido un bug menor en la creación de planetas (By Kloud).-
- [Fix][Bug #-------] Corregido un bug menor en el js que maneja los recursos al enviar una flota (By Neko).-

- [Cambio] Actualizados los links de soporte y reporte de bugs del overview en el panel administrativo.-
- [Cambio] Mejoras en los reportes.-
----------- Ahora en la primer ronda siempre se listan las flotas completas.-
----------- Ahora las sondas son destruidas en la primer ronda; al atacante le sale destrucción en la primer ronda, al defensor el reporte completo.-
----------- Ya no se duplica la primer ronda en las batallas de 2 rondas; en la primera se muestran las flotas completas y en la segunda el mensaje destruido.-
- [Cambio] Modificada la licencia para el 2010.-
',

'2.9.3' => ' 12/02/10
- [Seguridad] Mejoras de seguridad en el movimiento de recursos (By calzon).-

- [Fix][Bug #0000040] Multiplicación de recicladores (By tomtom).-
- [Fix][Bug #-------] Bug que permitía el incremento de naves en el salto cuántico (By war4head).-

- [Cambio] Nuevamente se volvio al viejo reloj en la visión general.-

# Version 0.2 del panel administrativo

.- Los moderadores y operadores ya no podran:
 || Suspender administradores.
 || Crear usuarios con rango mayor al de un jugador.
 || Reiniciar universo.
 || Ejecutar consultas SQL.
 || Editar datos personales.
 || Editar/Vaciar el historial.
.- [MINOR ADD] Adherida la ID en el sistema de suspensión.
.- Agregado un historial en el panel administrativo, la cual custodiara los movimientos de los siguientes archivos:
 || BanPage.php
 || ErrorPage.php
 || AccountEditorPage.php
 || Moderation.php
 || QueriesPage.php
 || ResetPage.php
 || AccountDataPage.php
 || CreateNewUserPage.php
 || GlobalMessagePage.php
 || DataBaseViewPage.php
 || ConfigStatsPage.php
 || SettingsPage.php
 || SearchInDBPage.php
.- Logs protegidos con .htaccess para que nadie pueda leerlos.
.- Agregada opción de modo vacaciones en Datos Personales.
.- Agregado de vuelta el scrolling en el frame (No funciona en IE).
.- Pequeño fix en la lista de mensajes.
.- Las siguientes funciones han sido reemplazadas por una redirección al buscador avanzado:
 || Lista de jugadores.
 || Lista de lunas.
 || Lista de planetas.
 || Lista de usuarios conectados.
 || Lista de planetas activos.
.- Agregado al buscador avanzado:
 || Expandir / contraer.
 || Paginación.
 || Nuevos filtros y tipos de búsqueda.
.- [FIX] borrar usuarios (No borraba las colonias de la tabla xgp_planets).
.- [FIX] bugs pequeños en el ejecutador de consultas SQL.
.- Cambios en el ADMIN.mo y traduccion completa al inglés. (Gracias Arali)
.- Agregada nueva función -> DeleteSelectedPlanet.
',

'2.9.2' => ' 01/12/09
- [Seguridad] Cerrado un agujero en la alianza que permitía sql injection (Gracias a privatethedawn).-

- [Novedad] Implementado un nuevo sistema de Plugins v0.3 (Gracias adri93).-
- [Novedad] Ahora cuando hay muchos recursos en la cuenta de un usuario se muestra K,M,B,T,T+ en la información de cuentas del panel del admin.-
- [Novedad] Hora y fecha del servidor, en el overview, en Castellano. (Sustituir en la linea 402 el (es_ES), por el idioma deseado.).-

- [Fix][Bug varios] Fueron corregidos varios bugs menores.-
- [Fix][Bug #0000019] Famoso error en las estadísticas provocado por una query que no era finalizada.-
- [Fix][Bug #0000020] Información de cuentas en el panel del admin.
- [Fix][Bug #-------] No se muestra bien el tiempo de estacionar en aliado en el panel del admin.-
- [Fix][Bug #-------] Corregidos varios textos (español,inglés) en el panel de administración.-
- [Fix][Bug #-------] Arreglado pequeño error en los datos mostrados en la seccion de recursos.-
- [Fix][Bug #-------] Ahora muestra correctamente la inactividad de los jugadores de tu alianza desde.-
- [Fix][Bug #-------] Al borrar un usuario se borraran todos sus planetas.-


- [Cambio] Algunas mejoras de optimización en la información de cuentas en el panel administrativo.
- [Otros]  Mejoras en el sistema de envio de flotas del FleetAjax.php
',

'2.9.1' => ' 01/11/09

- [Fix][Menor] Corregido un bug menor en el texto de los informes de espionaje que permitía bashing.-
- [Fix][Menor] Corregido un bug menor en el texto de los reportes de combates.-
- [Fix][Menor] Corregido un bug menor que mostraba carácteres raros en la alianza luego de editar un texto (By Neko).-
- [Fix][Menor] Corregido un bug menor que deformaba la página de los mensajes al enviar mensajes muy largos (By Neko).- 
- [Fix][Menor] Correciones en algunos textos.-

Nueva numeración (bug tracker)
- [Fix][Bug #0000010] No direcciona bien el ataque.-

Vieja numeración
- [Fix][Bug #5] Los sacs no coordinan los tiempos.-
- [Fix][Bug #6] Los sacs no aparecen en el mismo mensajes en la visión general (By privatethedawn).-


- [Cambio] Ahora cuando se estan moviendo flotas no es posible abandonar un planeta (By privatethedawn).-
- [Cambio] Ahora al explorar la galaxia, si es tu propio sistema solar, no perderás deuterio.-
- [Cambio] Nuevo panel administrativo (By Neko)
.- Nuevo skin
.- Agregado el sistema de moderación.
.- FIX suspender usuario (Si el jugador ya estaba suspendido y lo suspendias de vuelta creaba 
otra tabla con los mismos datos (duplicacion)). Mejoras.
.- Reset de universo mejorado, con poder de reiniciar distintas cosas.
.- [FIX] Al resetear todo el universo: 
 || no introducia el "id_level" en la tabla de planetas.
 || dejaba al usuario como inactivo (al reiniciar nuevamente se borraban las cuentas por inactividad).
.- Opciones de lunas mejorado.
.- Agregada la creación de planetas.
.- Contador de caracteres para mensajes (cntchar.js) nuevo y mejorado.
.- Agregada la opcion de "ver todos los mensajes" y un "seleccionar todo" en la lista de mensajes.
.- Agregado sistema para crear cuentas.
.- Reestructuración del adminresources.php (ahora AccountEditorPage.php)
 || Agregada la edición de datos personales.
 || Agregada varias opciones más para la edición de planetas y lunas.
 || Agregada la edición de oficiales.
.- Agregado un buscador avanzado.
.- Eliminado el borrado de reportes cuando se borraba un usuario, sino cuando el otro atacante o defensor queria ver
la batalla no le figuraba.
',


'2.9.0' => ' 21/10/09

- [Seguridad] Ahora es encriptada la contraseña que se ingresa durante la actualización.- 

- [Cambio] Optimización y mejora general en el manejo de las lunas (By angelus_ira).-
- [Cambio] Mejora en el rendimiento de algunas páginas.-
- [Cambio] Ahora al explorar la galaxia, si es tu propio sistema solar, no perderás deuterio.-

- [Fix][Bug #122] Problemas en la actualización de los puntos.-
- [Fix][Bug #123] Bug en los reportes mostrando que se produjo un empate cuando no fue asi.-
- [Fix][Bug #124] Problemas con el phalanx.-
- [Fix][Bug #125] Bug en la lista de amigos no permite aceptar/rechazar.-
- [Fix][Bug #126] No le aparecen los reportes de ataque al defensor.-
- [Fix][Bug #127] Problema con el title de los recursos maximo en las flotas.-
',

'2.8' => ' 10/10/09

- [Seguridad] Mejora de seguridad en varias funciones y archivos.-
- [Seguridad] Mejora de seguridad y prevención de números y caracteres no permitidos en el salto cuántico (Gracias a Trojan).-

- [Novedad] El pack en inglés se encuentra 100% traducido (Gracias war4head).-
- [Novedad] Se definieron constantes para los oficiales, asi es más fácil editarlos (no lo recomiendo).-

- [cambio] Mejoras varias en el script que calcula los ataques.-
- [Cambio] Corregido un bug de seguridad al agregar recursos al envio de una flota (Gracias a MSW).-
- [Cambio] Los reportes de combate de la sección mensajes ahora solo muestran un link al reporte y no un resumen.-
- [Cambio] Pequeño fix y mejora de seguridad para el phalanx y el alcance (Gracias a Trojan).-
- [Cambio] Optimizado el alcance del phalanx, reducida la carga de procesamiento (Gracias a Trojan).-
- [Cambio] Optimizados los movimientos de flotas (Gracias a Trojan).-
- [Cambio] Los recicladores valen ahora 10.000 de metal - 6.000 de cristal - 2.000 de deuterio como corresponde.-

- [Fix][Varios] Corrección de varios bugs menores globales.-
- [Fix][Varios] Corregidos varios bugs menores en el armado de las estadísticas.-
- [Fix][Varios] Corregidos varios bugs menores en los movimientos de flotas y misiones.-
- [Fix][Bug #108] Coordenadas erroneas en el mensaje de reciclaje.-
- [Fix][Bug #109] Los jugadores de la lista de compañeros aparecen siempre como desconectados.-
- [Fix][Bug #110] Duplicación de recicladores al hacer multiple clicks en la galaxia.-
- [Fix][Bug #111] Jugadores inactivos y borrados no son eliminados fisicamente.-
- [Fix][Bug #112] No son actualizados los recursos del planeta de un jugador al ser atacado.-
- [Fix][Bug #113] Pequeño bug que muestra un mensaje de error en la pagina de los sacs.-
- [Fix][Bug #114] Bug al intentar eliminar un jugador.-
- [Fix][Bug #115] Diferencia de estados entre (debil, fuerte) al enviar una flota y la galaxia.-
- [Fix][Bug #116] Bug en la misión de destrucción.-
- [Fix][Bug #117] Bug en la alianza que no descuenta los jugadores que salen de ella.-
- [Fix][Bug #118] Bug que permite ignorar las naves e investigaciones, permitiendo edificar robots, nanos y laboratorio.-
- [Fix][Bug #119] Bug en los textos de transportes.-
- [Fix][Bug #120] Bug en las batallas de una sola ronda.-
- [Fix][Bug #121] Bug en las solicitudes de la alianza.-
',

'2.7' => ' 17/09/09

- [Novedad] Sistema para ver toda la información de la cuenta de un usuario. By Neko.-
- [Novedad] SACs implementeados [BETA].-
----------- Código: MadnessRed
----------- Testeos y funcionamiento, como la adapación a la 0.9a: cyberrichy
----------- Adaptación a la 2.7: lucky

- [Fix][Varios] Varios bugs menores de estilo, plantillas y lenguaje reparados.-
- [Fix][Bug #93] Bug al eliminar un jugador.-
- [Fix][Bug #94] No se muestra la cantidad de flotas en el panel de administración.-
- [Fix][Bug #95] Deuterio en negativo a causa de la planta de fusión.-
- [Fix][Bug #96] Plantillas para solicitud de amigos descolocada.-
- [Fix][Bug #97] No se muestra el estado de baneo y vacaciones en los jugadores dentro del panel de administración.-
- [Fix][Bug #99] Problemas con la página de busqueda mostrando demsiados resultados o no permitiendo descender en la página.-
- [Fix][Bug #100] Al mandar naves de distintos tipos a colonizar vuelve el colonizador y se pierden las otras naves.-
- [Fix][Bug #101] No se generan lunas cuando sale un 20% de probabilidad.-
- [Fix][Bug #102] Al salir de una alianza el número de miembros no disminuye como debería.-
- [Fix][Bug #103] Problemas al actualizar las estadísticas.-
- [Fix][Bug #104] Cuando construis nanobots o robots te permite hacer naves y defensas.-
- [Fix][Bug #105] Bug menor en la alianza la querer editar el rango de un usuario cuando no existen rangos.-
- [Fix][Bug #106] Se encuentran invertidas las temperaturas de las lunas.-
- [Fix][Bug #107] Los planetas destruidos son contados como planetas activos.-
',

'2.6' => ' 24/08/09

- [Fix][Varios] Varios bugs menores de estilo, plantillas y lenguaje reparados.-
- [Fix][Bug #28] Bug en la cola de produccion del hangar.-
- [Fix][Bug #73] Problemas con el reporte de porcetaje de luna.-
- [Fix][Bug #78] Problemas con la lista de construcción.-
- [Fix][Bug #82] Se pueden mover flotas desde la galaxia cuando un jugador esta en modo vacaciones.-
- [Fix][Bug #83] Los reportes de ataque muy largos no se pueden ver completos.-
- [Fix][Bug #85] La cantidad de recursos para construir siempre se queda en rojo.-
- [Fix][Bug #84] Si la luna esta llena, no te permite desmontar edificios.-
- [Fix][Bug #86] Bug que provoca errores en las alianzas.-
- [Fix][Bug #87] Bug en el estilo del panel de administración, mostrando imagenes del juego.-
- [Fix][Bug #88] Bug menor que muestra un texto que fue utilizado para debug.-
- [Fix][Bug #89] Bug que permite la ampliacion del hangar cuando se estan construyendo flotas y defensas.-
- [Fix][Bug #90] Problemas con la misión de destrucción.-
- [Fix][Bug #91] No funciona correctamente el almirante.-
- [Fix][Bug #92] No funciona correctamente el almacenista.-

- [Cambio] Ahora la lista de planetas se muestra en el panel de admnistración por id de menor a mayor.-
',

'2.5' => ' 18/08/09

- [Novedad] Sistema de advertencias y errores en el panel de administración (también informa la existencia de una nueva versión).-
- [Novedad] Ahora puedes determinar si al banear un jugador este debe entrar o no en modo vacaciones.-
- [Novedad] Gracias a 3R1K que tradujo el panel de administración en su totalidad al idioma inglés.-
- [Novedad] Rediseñado el panel de administración.-
- [Novedad] Ahora la página de mensajes respeta el formato del OGame original, mostrando a los operadores del juego.- 
- [Novedad] Ahora cuando borras un planeta aparece planeta destruido como en el OGame original.-
- [Novedad] El misil interplanetario ahora requiere el hangar en nivel 1 y el motor de impulso en 1 como en el OGame original.-
- [Novedad] El misil de intercepción ahora requiere el hangar en nivel 1 como en el OGame original.-
- [Novedad] El silo ahora requiere el hangar en nivel 1 como en el OGame original.-

- [Fix][Varios] Varios bugs menores de estilo, plantillas y lenguaje reparados.-
- [Fix][Bug #14] No se pueden usar skins externos.-
- [Fix][Bug #47] Bug que provoca en IE que no se vea el menu y quede desplazado hacia arriba al bajar demasiado en una página.-
- [Fix][Bug #61] Problemas en el conteo de los jugadores en la visión general.-
- [Fix][Bug #62] Pequeño bug que no muestra los stats correctamente en el overview.-
- [Fix][Bug #63] Bug en la función del manejo de las sesiones.-
- [Fix][Bug #64] Bug con los permisos y con las lunas, evitando que sean atacadas.-
- [Fix][Bug #65] Bug que no permite espiar desde flota pero si desde la galaxia.-
- [Fix][Bug #66] Problemas en los niveles del phalanx.-
- [Fix][Bug #67] Producción ilimitada de misiles.-
- [Fix][Bug #68] Bug en el link para Continuar/Volver luego de enviar un mensaje circular en la alianza.-
- [Fix][Bug #69] Bug que muestra mal los mensajes de error cuando un usuario no esta logueado.-
- [Fix][Bug #70] Problemas con el salto de línea en los mensajes circulares.-
- [Fix][Bug #71] Problemas con la misión estacionar aliado, muestra un mensaje de que el jugador es muy fuerte.-
- [Fix][Bug #72] Bug en la diferencia de energía consumida actual, anterior y posterior.-
- [Fix][Bug #74] No se puede abandonar la alianza.-
- [Fix][Bug #75] Problemas en la actualización de puntos de algunos jugadores.-
- [Fix][Bug #76] La misión de espionaje ignora el estado de vacaciones desde la visión de galaxia.-
- [Fix][Bug #77] Al hacer regresar una flota con misión mantener posición no descuenta los tiempos correctamente.-
- [Fix][Bug #79] Bug que permite incrementar las tecnologías (Gracias a mikey302 y death).-
- [Fix][Bug #80] Bug en el limite de envio máximo de expediciones.-
- [Fix][Bug #81] Bug en la visión general de la alianza, mantiene el nombre "Fundador" del fundador de la alianza.-

- [Cambio] Ahora el número de la versión se obtiene de la base de datos.-
- [Cambio] Mejoras de seguridad en la página de la alianza.-
- [Cambio] Noticias removidas.-
- [Cambio] Mejorada la velocidad en que se genera la página de estadísticas y algunos cambios menores.-
- [Cambio] Nuevas alertas de presencia del directorio install y de escritura del archivo config.php.-
- [Cambio] Los recicladores ahora sólo pueden transportar 20.000 unidades de recursos como en el OGame original.-
- [Cambio] Removida la página de contacto, ahora puedes encontrar a los adms/mods/oper en el panel de mensajes como en el OGame.-
- [Cambio] Ahora a la derecha del nombre de la luna aparece la referencia (Luna) como en el OGame original.-
- [Cambio] Ahora si en la visión general tienes seleccionada la luna no se mostrará esta otra vez a la izquierda.-
- [Cambio] Reformas en la galaxia en código y organización de las clases y plantillas.-
- [Cambio] Juego adaptado al skin original del OGame, ahora el juego será a su vez compatible con cualquier skin.-
',


'2.4' => ' 25/07/09

- [Seguridad] Protección con .htaccess de la carpeta includes, language y templates.-
- [Seguridad] Protección con .htaccess de common.php, config.php y extension.inc.php.-
- [Seguridad] Cambio en el protección de carpetas.-
- [Seguridad] Mejoras de seguridad en diversos archivos.-

- [Novedad] Nuevo sistema de estadísticas, más rápido, más simple, menos querys, menos carga, y nuevo panel para administrarlas; (By angelus_ira) Muchas gracias =) .-
- [Novedad] Nuevo sistema de idioma, ahora el idioma es configurable desde el panel de administración, seleccionando un lenguaje a la vez.-
- [Novedad] La tecnología de espionaje funciona como en el ogame original:
-------- Nivel 0 a 1 -> Sin datos de la flota.-
-------- Nivel 2 a 3 -> Número total de naves.-
-------- Nivel 4 a 7 -> Número total de naves y el tipo de las naves.-
-------- Nivel 8 -> Número total de naves, tipo de nave y cantidad de cada tipo.-

- [Novedad] Ahora el administrador puede decidir si los administradores y/o moderadores se les actualizarán o no los puntos.-
- [Novedad] Ahora el administrador puede decidir si los administradores y/o moderadores pueden o no recibir ataques de otros jugadores.-
- [Novedad] Ahora en las investigaciones te muestra el nivel de espionaje y computación que te dan los comandantes.-
- [Novedad] Cuando un usuario es baneado, ahora le muestra la fecha en que finaliza su ban.-

- [Fix][Varios] Corregidos varios bugs menores de redirección, visuales, textos y configuraciones.-
- [Fix][Bug #1] Al achicar la pantalla se superpone el menu de recursos con el resto del cuerpo.-
- [Fix][Bug #2] Ahora cuando una cuenta es baneada, el jugador baneado no podrá acceder a la cuenta de ningun forma.-
- [Fix][Bug #3]	Al borrar una luna, ya no borrará el planeta.-
- [Fix][Bug #4] Reparados algunos bugs en el phalanx.-
- [Fix][Bug #8] Corregida la visión del imperio, ahora muestra las investigaciones.-
- [Fix][Bug #9] Ahora al cerrar el servidor un usuario común no podrá ingresar.-
- [Fix][Bug #10] Problemas en los permisos de las alianzas.-
- [Fix][Bug #11] Ahora al cerrar el servidor no desaparece más el mensaje con el motivo para cerrarlo.-
- [Fix][Bug #12] Ahora la misión desplegar funciona correctamente.-
- [Fix][Bug #13] La misión estacionar en aliado funciona correctamente, no se repiten más mensajes, ni tampoco produce tiempos negativos cuando se selecciona como tiempo de estacionamiento 0 horas.-
- [Fix][Bug #15] Problemas con la misión de recoleccion o reciclaje, no entrega los recursos a quien corresponde.-
- [Fix][Bug #16] Problemas con los tiempos del hangar al cambiar de planeta, volviendo el tiempo a su estado incial.-
- [Fix][Bug #17] Los oficiales almirante y general funcionan correctamente [oficiales al 100%].-
- [Fix][Bug #18] Problemas con la planta en nivel 1 permitiendo la produccion de recursos (Fixed by zorro2666).-
- [Fix][Bug #19] Ahora si un jugador es fuerte o débil y al mismo tiempo está inactivo podrá ser atacado de todas formas.-
- [Fix][Bug #20] Problemas de caracteres en los textos de registro.-
- [Fix][Bug #21] No funciona el enviar mensaje cuando recibes un ataque.-
- [Fix][Bug #22] Problemas con los mensajes al redactar un mensaje privado en la parte visual.-
- [Fix][Bug #23] Fallan los links que redirigen a la galaxia durante los movimientos de flotas.-
- [Fix][Bug #24] El hangar permite producir igual durante su ampliación (Fixed by zorro2666).-
- [Fix][Bug #25] Se puede ampliar el hangar mientras de produce (Fixed by zorro2666).-
- [Fix][Bug #26] No sale el nombre del jugador en color verde en las estadísticas.-
- [Fix][Bug #27] Bug que permite ampliar el laboratorio e investigar al mismo tiempo.-
- [Fix][Bug #29] Bug en el select de las estadísticas al cambiar la página mostrando la primer página siempre.-
- [Fix][Bug #30] Problemas con los campos del planeta y las colas de contrucción.-
- [Fix][Bug #32] Bug para ver el reporte de combate desde el panel de administración.-
- [Fix][Bug #33] Bugs diversos menores en los misiles interplanetarios (Fixed by lordz).-
- [Fix][Bug #34] Bugs menores que no muestran correctamente los textos.-
- [Fix][Bug #35] Posible bug en los almacenes, incrementan menos de lo que deben.-
- [Fix][Bug #36] No aparece el icono para lanzar misiles en la galaxia.-
- [Fix][Bug #37] Bug que provoca que las colonias en la visión general se vean en filas de 5 y no de 2.-
- [Fix][Bug #38] Bug en la misión colonizar, si se alcanzó el limite de planetas entonces regresa instantaneamente sin respetar los tiempos.-
- [Fix][Bug #39] Bug en las expediciones que provoca la perdida de la materia oscura.-
- [Fix][Bug #40] Bug que no muestra la materia oscura recolectada en las expediciones.-
- [Fix][Bug #41] Pequeño bug que provoca un error en el update de puntos (Fixed by angelus_ira).-
- [Fix][Bug #42] Problemas con los textos, se pierden las frases y la cantidad de recursos en algunos reportes.-
- [Fix][Bug #43] Problemas con la proteccion de novatos al enviar sondas desde la galaxia y desde flota.-
- [Fix][Bug #44] Problemas con el js que actualiza los recursos, no teniendo en cuenta la velocidad del servidor.-
- [Fix][Bug #45] Los almacenes funcionan mal, debido a un bug en la relación de incremento por nivel.-
- [Fix][Bug #46] bbCode ni HTML funcionan en los textos de la alianza.-
- [Fix][Bug #48] Se perdió el texto de advertencia en las expediciones.-
- [Fix][Bug #49] Problemas con la actualización de puntos de la alianza.-
- [Fix][Bug #50] Problemas con los atajos.-
- [Fix][Bug #51] No aparece el icono de los mensajes para responder.-
- [Fix][Bug #52] Bug en la alianza que probaca un error en la base de datos.-
- [Fix][Bug #53] Problemas de duplicación en la misión transportar.-
- [Fix][Bug #54] Bug que no permite generar las estadísticas (Fixed by angelus_ira).-
- [Fix][Bug #55] Bug que mostraba la luna luego de que fuera destruida.-
- [Fix][Bug #56] Bug que no muestra el porcentaje de creación de las lunas.-
- [Fix][Bug #57] Bug que provoca que no se muestren los mensajes en las expediciones.-
- [Fix][Bug #58] No se muestran los informes de construcción en el panel del admin.-
- [Fix][Bug #59] Los reportes de construcción no figuran en ninguna categoría.-
- [Fix][Bug #60] Bug que permitia obtener tecnologías sin ningún costo.-

- [Cambio] Optimización del manejo de flotas (By shoghicp).-
- [Cambio] Optimización del overview del admin (By jtsamper).-
- [Cambio] Reorganizadas algunas carpetas.-
- [Cambio] Ahora los administradores no podrán borrarse a si mismos.-
- [Cambio] Ahora sólo los administradores podrán borrar jugadores.-
- [Cambio] Reducción de líneas y simplificación en el panel de admnistración.-
- [Cambio] Finalmente el directorio includes queda definido con 3 carpetas (classes, functions y pages).-
- [Cambio] Integrado el auto-update a index.php en la carpeta install.-
- [Cambio] Cuando se registra un usuario ahora es redirigido directamente dentro del juego.-
- [Cambio] Se volvio al viejo sistema de mensajes.-
- [Cambio] El pack de idioma de divide en 4 archivos:

-------- ADMIN.mo -> TODO LO DEL PANEL DE ADMINISTRACIÓN.-
-------- CHANGELOG.mo -> CHANGELOG TAL Y CUAL COMO ES CONOCIDO.-
-------- INGAME.mo -> TODO EL CONTENIDO INTERNO DEL JUEGO Y NO ACCESIBLE SIN TENER UNA CUENTA.-
-------- PUBLIC.mo -> TODO EL CONTENIDO PÚBLICO DEL JUEGO (index.php, reg.php, clave perdida y contacto).-

- [Cambio] Ahora en la página de la flota se muestra correctamente la velocidad de las naves al posicionar el mouse sobre el nombre de las mismas.-
- [Cambio] Ahora al realizar cualquier movimiento de flota desde la galaxia, también se pasa el valor de la misión por lo que si por ejemplo seleccionas atacar ya aparecerá marcado en el envío de las flotas.-
- [Cambio] Ahora si el jugador esta inactivo podrá ser atacado, funcionando de la misma forma que en el ogame original.-
- [Cambio] Los archivos ShowFleetPage.php, floten1.php, floten2.php, y floten3.php ahora utilizan plantillas.-
- [Cambio] Finaliza el soporte a las siguientes versiones v1.4a/v1.4b/1.4c,por lo tanto no habrá más auto-update.php.-
- [Cambio] class.FlyingFleetHandler.php maneja todo lo que sea flotas, misiones y funciones especificas de las mismas.-
- [Cambio] Eliminada la experiencia de guerrero y minero.-
- [Cambio] Las funciones CreateFleetPopupedMissionLink, CreateFleetPopupedFleetLink y BuildHostileFleetPlayerLink fueron hubicadas en class.FlyingFleetsTable.php.-
- [Cambio] Dentro de la clase class.FlyingFleetsTable.php, encontramos las funciones BuildFleetEventTable y BuildFlyingFleetTable y todas las funciones que estas necesitan para funcionar.-
- [Cambio] rw.php ahora se llama CombatReport.php.-
- [Cambio] raketenangriff.php del root cambiado a MissilesAjax.php; y flottenajax.php cambiado a FleetAjax.php.-
- [Cambio] La función GetNextJumpWaitTime fue movida de GeneralFunctions.php a class.ShOWInfosPage.php.-
- [Cambio] infos.php y jumpgate.php se encuentran en class.ShOWInfosPage.php.-
- [Cambio] Eliminado quickfleet.php no tenía utilidad.-
- [Cambio] Home reprogramado y simplificado.-
- [Cambio] Ahora los oficiales espía y comandante son obtenidos desde la base de datos directamente y no suman espionaje y computación, asi es más fácil de administrar.-
- [Cambio] constantes.php nuevamente se llama constants.php y funciones.php se llama ahora GeneralFunctions.php.-
- [Cambio] class.ShowShipyardPage.php maneja la construcción de defensas y naves.-
- [Cambio] Implementado el diseño del OGame original para el overview.-
- [Cambio] Adaptado el diseño de la página de los oficiales al resto del juego.-
- [Cambio] En la visión de la galaxia no se realizan mas revisiones de los campos de los planetas.-
- [Cambio] La función sendnewpassword fue integrada al lostpassword en el archivo index.php.-
- [Cambio] Limitada la query que traia los datos en la visión del imperio, reducida un poco la carga.-
- [Cambio] Las funciones de Strings.php fueron movidas a funciones.php.-
- [Cambio] Ahora si no tenes materia oscura para reclutar oficiales aparecerá el reclutar en rojo y bloqueado.-
- [Cambio] Notar que el manejo de las páginas esta en game.php.-
- [Cambio] Todas las páginas del juego ahora se manejan con funciones y/u objetos.-
- [Cambio] Inicio de la exportación del código a objetos.-
- [Cambio] Eliminados los title de las páginas, ahora sólo se muestra el nombre del servidor.-
- [Cambio] Ahora la base lunar sólo da 3 campos por nivel, tal cual y como es en el ogame original.-
- [Cambio] Ahora para mostrar que un usuario fue baneado, se imprime un texto y no se recurre a una plantilla.-
- [Cambio] Ahora no aparecen más mensajes de confirmación al editar, crear o borrar una nota.-
- [Cambio] Ahora las notas nuevamente se abren en un pop-up.-
',

'2.3' => ' 30/05/09

- Corregido un bug que no permitía cambiar el rango a los miembros dentro de las alianzas.-
- Varios cambios en el panel del admin:

-------- Integrados todos los archivos de lenguaje.-
-------- Renombrado el archivo para el reset del universo.-
-------- Unos cuantos cambios en mats.php, tanto de estilos como limpieza de código.-
-------- Cambios en el left-menu del panel de administración.-
-------- Renombrados algunos archivos y realizadas algunas correciones menores.-
-------- Unos cuantos ajustes en los textos y plantillas de varios archivos.-
-------- Mensaje de confirmación al intentar eliminar a un jugador del servidor.-
-------- El archivo mats.php ahora se llama adminresources.php, ademas se le integro todos los archivos que manejaban las tecnologías y recursos.-
-------- Solucionado el bug de la actualización de puntos.-
-------- Nueva función (optimizar tablas) (By Saint).-
-------- Corregido un bug que al crear una luna no le asignaba el nombre seleccionado.-
-------- Nueva función (eliminar luna) (By tonique).-
-------- Corregido un bug que en la lista de lunas no mostraba el id real de la luna.-
-------- Si el usuario no tiene nivel 1 al menos no podrá ver nada del panel de administración.-
-------- Optimizadas algunas querys en messall.php
-------- Eliminado el QueryExecuter.php

- Se finalizá el soporte a las versiones v1.4d/v1.4e/v1.4f y también el auto-update de las mismas.-
- Eliminadas algunas querys innecesarias de MissionCaseMIP.php.-
- Se elimino una query innecesario de MissionCaseRecycling.php.-
- Limpieza de la base de datos de campos innecesarios dentro de la tabla users.-
- Algunos cambios y mejoras en el update.php.-
- Ahora sólo sumarán puntos de guerrero los jugadores que realicen batallas y no ambos.-
- Reparado un bug que mostraba el mensaje de la flota al finalizar la misión.-
- La experiencia del minero ya no se muestra más con decimales.-
- Se volvió al antiguo diseño de la construcción de edificios.-
- Corregido un bug que en al finalizar una construcción en el overview aparecía Construcción() en vez de Libre.-
- Algunas modificaciones en los créditos.-
- Modificados algunos aspectos en el aspecto interno del juego.-
- Cambiada la imagen dentro del juego.-
- Rediseñado el index, espero que les guste.-
- Agregado un favicon, cada uno podrá cambiarlo por el que quiera.-
- Eliminados algunos residuos provenientes de otras versiones.-
- Eliminados los frames, ahora el menu se muestra con la función ShowLeftMenu.php y algo de estilos css para ajustar todo.-
- Cambiados todos los $xnova_root_path por $xgp_root.-
',

'2.2' => ' 06/05/09

- Se reemplazo el menú derecho e izquierdo por el original, y la imagen del fondo también.-
- Revisado todo messages.php,se eliminaron querys innecesarias, se organizo el código, se restringieron algunas querys para optimizar la página, se elimiaron elementos sin utilidad y se integro el lenguaje.-
- Toda la galaxia fue revisada, se hicieron algunas correciones en los textos y algunas mejoras visuales.-
- Corregido un bug que impedia cambiar la cantidad de planetas, sistemas y galaxias que se podían utilizar en el universo (modificable desde constantes.php), recomiendo dejarlo en 9-499-15, asi no saturan mucho el juego.-
- Renombrado functions.php a funciones.php
- Limpieza y revisado de constants.php, renombrado a constantes.php.-
- Corregido un bug en la página de tecnologías.-
- Eliminadas las vars de los mensajes, no tenían utilidad.-
- Se revisaron nuevamente todas las funciones y fueron nuevamente reasignadas utilizando el sistema de funciones_A y funciones_B.-
- Solucionado el bug de las páginas en blanco, dejando la versión bastante estable [BETA].-
- Nueva forma de distribuir las funciones, en A y B. Para mas detalles lean la información que deje comentada en commons.php, esto es provisorio, aunque creo que es la mejor forma de agilizar el juego, y tenerlo más estable.-
- Pequeño cambio en el ingreso, eliminando algunas lineas.-
- Algunos cambios en el commons.php y eliminadas algunas cosas innecesarias.-
- Corregido un pequeño bug en las listas de lunas.-
- Corregido un bug en la opción de crear lunas.-
',

'2.1' => ' 02/05/09

- Pequeño cambio en el index y la selección de la página.-
- Incluido reg.mo directamente a reg_form.tpl y reg.php.-
- Modificado el diseño del registro y de la página de clave perdida.-
- Correcciones gráficas en el index.-
- SACS funcionando al 50% [problemas en la coordinación de los tiempos y en la visión de los movimientos de flotas].-
- Cambio visual en las estadísticas, ahora el *, +1 y -1 (rankplus), se muestra con js(overlib).-
- Limpieza en commons.php con lo que durante el movimiento de flotas reducira la carga del juego.-
- Ahora al realizar un espionaje ya no aparecerá la página en blanco ni tampoco tirara error.-
- Corregidos unos cuantos bugs provocados por la distribución de las funciones.-
- Nuevas imágenes de planetas, mucho más vistosas.-
- Corregido un pequeño bug que no permitía ver las páginas publicas(contact.php, reg.php, credit.php y la sección de clave perdida).-
',

'2.0' => ' 23/04/09

- Visión general del panel del admin mejorada, ajustada mejor la tabla e integrado el lenguaje a las plantillas.-
- Simplificación del sistema de créditos, e integración del lenguaje.-
- Integrado el idioma a resources.php y a las respectivas plantillas.-
- Cambios en las tablas de las estadísticas, inclución del lenguaje a las plantillas, revisión del código y algunas mejoras en la carga.-
- Algunos cambios visuales en fleet.php.-
- Eliminada la función AdminMessage, cumplia la misma función que message.-
- Optimización e integración del lenguaje a admin/settings.php
- Algunas correcciones que previenen que por la actualización de puntos se provoque un bug en el panel de administración.-
- Optimizadas algunas querys de las flotas en commons.php.-
- Correciones en algunos textos en los mensajes de movimientos de flotas.-
- Mejora de seguridad, no podrás ver las páginas internas del juego sino te logueaste.-
- Ahora se pueden ver bien los mensajes de error e informes de los mensajes.-
- Corregido un bug que al abandonar un planeta no borraba la luna, y esta podía ser utilizada.-
- Simplificación y reorganización de BatimentBuildingPage.php.-
- Correcciones visuales en los edificios, y correciones de algunas tablas para ajustarlas mejor.-
- Nueva imagen de materia oscura en el menú superior, también se ampliaron los tamaños de las imágenes.-
- Reparado un bug que permitía mover flotas en modo vacaciones.-
- Traducido el mensaje del modo vacaciones, y corregido un bug que no mostraba el tiempo real de vacaciones.-
- Cambiados algunos $ugamela por $xnova.-
- Implementación de seguridad, fue renombrado el archivo extension.inc a extension.inc.php, no estaba protegido y podía leerse su contenido.-
- Algunas correcciones y simplificación del código en buddy.php.-
- Revisado todo el notes.php:

--------- Plantillas agregadas a su carpeta correspondiente "notes".-
--------- Algunas correciones visuales.-
--------- Integración del idioma a las plantillas.-
--------- Reparados algunos bugs.-
--------- Ahora al editar el mensaje, se muestra el asunto y el mensaje.-
--------- Conteo de caracteres en js aplicado.-

- Eliminadas algunas funciones de administración.-
- Reparado un bug que no mostraba el límite real de las flotas posibles a enviar.-
- Oficiales:

--------- Algunas correciones visuales.-
--------- Oficiales pendientes por reparar: Almirante y General.-
--------- Oficiales funcionando: Geólogo, Ingeniero, Tecnócrata, Constructor, Científico, Almacenista, Defensor, Bunker, Espía, Comandante, Destructor, Raider y Emperador.-
--------- Reparados los oficiales espía y Comandante.-
--------- Reparado el oficial empeador(By thyphoon) y destructor(By angelus_ira).-
--------- Integración del idioma a las plantillas y código.-

- Limpieza de scripts.-
- Re-organizadas todas las funciones del juego (optimizandolo increíblemente)(cada función se asigno a su archivo correspondiente).-
- Eliminado CombatEngine.php.-
- Algunas correciones en commons.php para agilizar el juego en general.-
- Limpieza y optimización del instalador.-
- La función doquery fue unificada también dentro de functions.php.-
- Las funciones de unlocalised.php fueron integradas a functions.php
- Limpieza de funciones inutiles en includes/functions:

--------- Eliminado RevisionTime.php.-
--------- Eliminado SecureArrayFunction.php.-
--------- Eliminado ResetThisFuckingCheater.php.-
--------- Eliminado ElementBuildListQueue.php, el archivo ElementBuildListBox.php cumple la misma función y se encuentra en uso.-

- Limpieza en functions.php,se borraron algunas funciones sin utilidad alguna.-
- Limpieza en unlocalised.php,se borraron algunas funciones sin utilidad alguna o vacías.-
- Se reorganizaron casi todas las plantillas y se borraron algunas más sin utilidad (algunas pedientes a organizar).-
- Se borraron todas las plantillas de la galaxia que no tenían utilidad(la galaxia la genera el código php dinámicamente).-
- Revisado todo el search.php:

--------- Borradas algunas lineas.-
--------- Reorganizado el código.-
--------- Reorganizadas las plantillas en una carpeta en templates.-
--------- Se integro search.mo a las plantillas.-
--------- Se corrigió un bug que no mostraba la alianza en la búsqueda por usuarios.-
--------- Se corrigió un bug que no redirigía correctamente a la vista de la alianza.-
--------- Se corrigió un bug dentro de la alianza para poder verla desde search.php

- Revisado todo el mercader:

--------- Adherido marchand.mo a sus respectivas plantillas.-
--------- Corregidas todas las plantillas y bugs en la muestra de los recursos(no aparecen más en eltop).-
--------- Simplificación del código php, reorganizado y reprogramado lo que no funcionaba bien.-
--------- Corregidas las validaciones, admiten ceros, pero no números negativos.-
--------- Añadidas las plantillas respectivas a una carpeta en templates(para una mejor organización).-

- Cookies.mo integrado a su archivo correspondiente.-
- Algunos textos fueron colocados en system.mo, ya que hacen al caracter general del juego, y no de un sector en especifico.-
- Optimizado MissionCaseAttack.php.-
- Optimizado el overview, se elimino código innecesario, se reorganizó, se eliminaron querys que no tenían utilidad y se integro el idioma a las plantillas.-
- Reubicados algunos archivos.-
- Limpieza de la base de datos, de cosas que no se utilizaban.-
- Reorganizado el menú de opciones, integración del idioma a la plantilla y se eliminaron querys innecesarias.-
- Algunos archivos de texto fueron integrados directamente a los archivos para agilizar el juego y su velocidad.-
- Se reorganizaron algunas plantillas y se eliminaron algunas otras inútiles.-
- Eliminados los emoticones.-
- Como siempre actualizados el auto-update y el instalador para que todo sea más facil.-
- Cambios en el instalador.-
- Optimizadas unas cuantas páginas.-
- login.php, lostpassword.php y logout.php unificados en el index.php mejorando un poco el rendimiento y organización.-
- Algunas correcciones visuales en la visión del imperio.-

- Revisada toda la alianza:

--------- Mejoras varias.-
--------- Mejoras en lenguajes.-
--------- Mejoras en plantillas.-
--------- Se agregaron validaciones.-
--------- Se reorganizó el código.-
--------- Se reparo el texto de las solicitudes, ahora podrás editarla.-
--------- Todos los mensajes ahora te redirigiran.-
--------- Se corrigió un bug en los rangos.-
--------- Se optimizó un poco, se eliminaron lineas inútiles y se fixearon algunos bugs.-
--------- Se repararon todos los errores encontrados en los textos y plantillas que no se mostraban, asi como cosas que no se realizaban.-

- Cuando un usuario falla al intentar el login ahora es redirigido al inicio.-
- Mejorado el index ahora funciona mucho más rápido.-
- Mejorados algunos textos en general, y corregidos algunos detalles.-
- Rediseñado el sistema de ingreso al panel del admin y regreso al juego.-
- Limpieza de archivos y residuos.-
- Eliminado el chat, loteria, razas, simulador, tutoria, records y todo aquello que no consideraba necesario.-
- Reprogramados los menús derechos e izquierdos.-

- Un resumen de las figuras más destacadas de este proyecto:

--------- Tomo las riendas sobre la 1.5b saltando a la 2.0 para trerles todas las mejoras enunciadas a continuación [By lucky].-
--------- Partiendo de la version 0.9a llegando hasta la 1.5b del XG Proyect por lucky, PowerMaster, Calzon, Tarta, Tonique y muchas personas más.-
--------- Continuado por UGamela Britania con varias mejoras, seguido por el equipo francés Raito, Chlorel, e-Zobar y Flousedid.-
--------- Proyecto ogame para todos y con todas las funciones iniciado por Perberos.-
',

'1.5b' => ' 03/04/09

- Cambios y correcciones en templates y textos.-
- Loteria reparada (By lucky).-
- Correciones en el instalador, soportando correctamente las razas, y también en el auto-update.-
- Razas corregidas (By Tonique).-
- Corregido un bug en el instalador.-
',

'1.5a' => ' 26/03/09

- Corregido el link de administración.-
- Mejoras en el instalador.-
- Fix corregido bug que mostraba mal la leyenda en la galaxia.-
- Actualizado el auto-update para poder pasar fácilmente de la versión 1.4f o de la 1.4c a la 1.5a.-
- Ahora la instalacion incluye la lotería y el chat, no deberás hacer nada manualmente.-
- Arreglada la página de amigos ahora debería mostrar bien a tus amigos y no a vos (By lucky).-
- Mejorado el auto-update de puntos, ahora podrás instalar sin realizar modificaciones en los archivos.-
- Unificamos la versión de XG Proyect con la de calzon.-
',

'1.4f' => ' 18/03/09

- Fix pequeñas correciones en la base de datos.-
- Fix pequeñas correcciones en traducciones generales.-
- Fix Corregidas variables en alianza, nueva estructuracion, mejor optimizada.-
- Mod Agregado terraformer y super terraformer a constants.php, (personalizable campos que dara cada uno).-
- Mod Administradores u operadores no aparecen mas Estadisticas.-
- Mod Completadas algunas imagenes faltantes en el skin, cambiada la de la supernova por una de mejor calidad.-
- Mod Optimizacion de consultas y variables generales (sistema mas limpio).-
- Mod Nuevo edificio, Super Terraformer, aumenta 10 campos por nivel (winjet).-
<font color="red">- Tecnologias y naves unicas de razas. 70% completado.-</font>
<font color="red">- Formas de Gobierno (democracia, socialismo y pirateria) 30% completado.-</font>
<font color="red">- Fix a bug destruccion de luna.-</font>
',

'1.4e' => ' 12/03/09

- Fix a textos e imagenes de naves y defensas nuevas asi como a razas.-
- Fix Enviar mutiples flotas, expediciones, misiones, al ir atras (modo test por ahora).-
- Fix Corregido bug en consumo de deuterio (flotenajax.php).-
- Fix corregido bug al abandonar colonias por fallo seguridad (overview.php).-
- Fix En Estadisticas aparecias en una alianza aunque ya hubieras salido (alliance.php).-
- Mod 4 Nuevas naves: Interceptor, Cazador Crucero, Transportador y Titan.-
- Mod 2 Nuevas defensas: Cañon de Fotones y Base Espacial.-
- Mod Nueva Tecnologia de Desarrollo, aumenta colas posibles a edificios.
<font color="green">- Mod Razas: Humanos, Aliens, Predators y Darks, con cada nivel aumenta:.-</font>
<font color="green">- Humanos: Mina Metal +3% produccion, +2% Ataque y Escudos.-</font>
<font color="green">- Aliens: Mina Cristal +3% produccion, +3% Blindaje.-</font>
<font color="green">- Predators: +10% Ataque.-</font>
<font color="green">- Darks: Sintetizador Deuterio +3% produccion, +4% Blindaje y Escudos.-</font>
',

'1.4d' => ' 09/03/09

- Fix algunas traducciones.-
- Fix Ajustado a resolucion 1024x768, reacomodo en columnas de edificios y frames.php-
- Fix multiplicacion/Duplicacion de ligeros y estrellas de la muerte (flotten1.php).-
- Fix Seguridad de carpetas, una mas, aparte de la que ya existia.-
- Fix en Mercader, devolvia recursos al meter numeros negativos (marchand.php).-
- Fix Misiles (projectxnova) adaptado y corregido a esta version (MissionCaseMIP.php).-
- Fix agregado entero en funcion investigaciones (ResearchBuildingPage.php).-
- Fix, pequeña correccion en alianzas rangos y administracion(alliance.php).-
- Fix, Correccion en Galaxia (galaxy.php).-
- Mod/Fix Arreglo a mensajes(project xnova) adaptado, corregido y aumentado para esta version.-
- Mod actualizacion automatica (ahora si es automatica) y no consume recursos.-
- Mod Edificios en columnas de 5.-
- Mod Menu Derecho agregada compatibilidad, reordenadas las funciones.-
- Mod Agregado Records (Records.php).-
- Mod Agregado Chat.-
- Mod Agregado Simulador de Batallas.-
- Mod Agregado Loteria (project xnova), adaptado y corregido a esta version.-
- Mod Reacomodo vision general (projectxnova), corregida compatilidad (overview.php).-
- Mod Recursos en tiempo real (tonique) modo test por ahora.-
- Borrado actualizacion automatica, consume muchos recursos (todos haciendo click a vision general).-
',

'1.4c' => ' 08/02/09

- Eliminados los recursos en tiempo real debido a que se quedaban congelados.-
- Reparados los oficiales espía y comandante.- (By jtsamper foro project.xnova.es)
- En la galaxia ya no puedes reciclar o espiar sin deuterio.-
- Prevenir números negativos y carácteres no numéricos en la galaxia (By neurus foro Xproject.xnova.es).-
- Ahora para ver la galaxia necesitas deuterio (Original project.xnova.es fixeado por lucky).-
- Al disolver una alianza esta ya no aparece en las estadísticas (By xesar foro project.xnova.es).-
- Corregida una redirección que funcionaba mal en la alianza.-
- Corregido un pequeño error de sintaxis en la flota que tiraba severos reportes de errores (Gracias edering).-
- Agregado un mensaje recordatorio de como se debe incrementar o eliminar la materia oscura (Gracias edering).-
- Anuncios eliminados (Por votación de los usuarios de XG).-
- El auto-update no soporta más las siguientes versiones:  v0.9a/v1.0a/v1.0b/v1.1a/v1.1b/v1.1c/v1.2a/v1.2b/v1.2c/v1.3a (Si tienes alguna des estas versiones deberás usar un update anterior).-
- Ahora en la busqueda al hacer click en el link te redirecciona al sistema del jugador y no al tuyo (By Anghelito).-
',

'1.4b' => ' 06/12/08

- Desbaneo reparado.-
- Oficiales reparados.-
- Ahora al iniciar sesión con tu cuenta, iniciará siempre desde el planeta principal y no desde una colonia.-
- Un moderador u operador ya no podrá cambiarse los permisos a Administrador.-
- Galaxia optimizada.-
- Ahora cuando colonizas tu planeta se llamará "Colonia" y no "Planeta Principal" (By lucky).-
- El auto-update no soporta más las siguientes versiones:  v1.0a/v1.0b/v1.1a/v1.1b/v1.1c/v1.2a/v1.2b/v1.2c/v1.3a (Si tienes alguna des estas versiones deberás usar un update anterior).-
- Corregidas algunas redirecciones y mejoradas otras.-
- Ahora puedes usar espacios en blanco en el nombre de tu planeta (By lucky).-
- Borrado de archivos innecesarios (esto no termina más).-
- Reparada la tabla que muestra las flotas en vuelo en el panel del admin.-
- Mejoras, organización, limpieza y optimización del lenguaje (No pongo más que cambie en los lenguajes porque ya es detallar mucho, para nada).-
',

'1.4a' => ' 06/12/08

- Reparado el reset del universo.-
- El auto-update no soporta más las siguientes versiones: v1.0a/v1.0b/v1.1a/v1.1b/v1.1c/v1.2a (Si tienes alguna des estas versiones deberás usar un update anterior).-
- Más limpieza de archivos innecesarios.-
- Limpieza y pulido del panel de admin (lenguaje).-
- Lista de planetas <-> Lista de usuarios cambiado (lenguaje - Gracias Alberto14).-
- Ahora puedes agregar y remover materia oscura desde el panel de administración (By lucky).-
- Actualización en tiempo real de los recursos (By Alberto14).-
- Cambidas las imágenes del XNova, por las imágenes del OGame original.-
- Borradas imágenes innecesarias.-
- Optimizadas las imágenes de los oficiales.-
- Eliminado el multi totalmente (A pedido del público).-
- Eliminados los records totalmente (A pedido del público).-
- Eliminado el chat totalmente (A pedido del público).-
- Traducidos algunos textos en el formulario de envío de mensajes (lenguaje).-
- Complementado el infos.mo con los datos del verdadero OGame (lenguaje).-
- Pulido y limpieza del search (lenguaje).-
- Pulido y limpieza del overview (lenguaje).-
- Pulido y limpieza del leftmenu (lenguaje).-
- Pulido y limpieza del registro (lenguaje).-
- Pulido y limpieza del login (lenguaje).-
- Cambios de lenguaje en notes.-
- Cambios en el login Contact -> Contacto y Forum -> Foros.-
- Eliminado player.mo - no tenía ninguna utilidad.-
- Limpieza del archivo de lenguaje login.-
- Reemplazados todos los "Titanio", "Silicio" y "Gashofa" por "Metal", "Cristal" y "Deuterio".-
- Correciones de lenguaje en el install y limpieza de dicho archivo (Gracias Alberto14).-
',

'1.3c DMV' => ' 30/11/08 "DMV = Dark Matter Version Exclusivo Xtreme-gameZ.com.ar"

- Correciones en los lenguajes de la supernova o super nave de batalla y el protector planetario (algo siempre me olvido).-
- Modificación de la ubicación de algunos arhcivos.-
- Eliminada una carpeta llamada .svn a la cual no le encontre utilidad.-
- Limpieza de archivos innecesarios y duplicados.-
- Implementada la materia oscura (Código 100% x lucky) (Gracias Reyndio por la idea).-
----- Los oficiales ahora se manejan por la materia oscura 1 punto oficial = 1000 materia oscura.-
----- En las expediciones se obtiene la materia oscura necesaria.-
----- No existen más los puntos de oficiales, aun asi se sube el nivel de minero y flota.-
----- Se siguen mostrando los registros de ataque.-
----- Auto-Update actualizado especialmente para soportar la materia oscura.-

- Ya no se pueden atacar lunas + fuerte o + debiles que uno (By Neurus).-
- Panel del admin, "Utilisateur?" -> "¿Usuario?", modificación en el lenguaje.-
- Por razones de seguridad elimine el phpinfo.-
- Panel del admin, "Lista de Usuarios" -> "Lista de Planetas", modificación en el lenguaje (Gracias Alberto14).-
- Solucionado el error en el orden por id de la alianza (By tarta).-
',


'1.3b EU' => ' 30/11/08

- No hace falta más ingresar el nombre del planeta, por defecto es "Planeta Principal".-
- Eliminadas imagenes del "sexo".-
- Optimizada la imagen del inicio, ahora carga más rápido.-
- Compatibilidad del auto-update con todas las versiones.-
- Nueva versión del auto update, más comprensible(creo).-
- Reparado el problema con la instalación (Gracias Anghelito).-
',

'1.3a' => ' 29/11/08

- XNova 100% TRADUCIDO AL ESPAÑOL [PUDE HABERME SALTEADO ALGO POR FAVOR AVISAR](By lucky).-
- Limpieza de scripts, eliminamos varios archivos de la carpeta scrips que notamos no necesarios.-
- Reparada la validación del index, ahora si la carpeta install existe no podras acceder al juego (By lucky).-
- Arreglado el modo vacaciones, ya no puedes entrar en vacaciones cuando estas atacando (By lucky).-
- No se muestran más los recursos negativos.-
- Redirección luego de enviar una flota (By tarta).-
- Ahora los días se muestran con una "d" y no con una "j" (By tarta).-
- Nuevamente agregamos los emoticons.-
- Ahora puedes cambiar el nombre en el juego, por fin solucionamos esto.-
- Nuevo diseño del auto-update, mucho mas vistoso y atractivo.-
- Reparada la instalación, ahora funcionan los misiles al instalar el juego.-
',

'1.2c EU' => ' 26/11/08

- Reparada la instalación.-
',

'1.2b' => ' 26/11/08

- Misiles finalmente funcionando (By lucky).-
- Desbaneo automático (By Anghelito).-
- Reparado el modo vacaciones.-
- Traducciones en varios archivos (By edering).-
- Reparado el modo debug (By tarta).-
- Reparado el link de las notas (By lucky).-
- Eliminada la carpeta emoticones.-
- Fix ranking de flotas en vuelo (By Pada).-
- Mejoras en archivos de lenguaje.-
- Cambios en el mensaje de bienvenida.-
- Records reparados.- (By tarta).-
- Actualizado el auto-update para poder actualizar: 0.9a -> 1.2a / 1.1b -> 1.2a / 1.2a -> 1.2b (By lucky).-
- Cambios en el instalador.-
- Se elimino una tabla que no hacia falta.-
',

'1.2a' => ' 19/11/08

- Actualizado el auto-update para poder actualizar: 0.9a -> 1.2a y 1.1b -> 1.2a .-
- Reorganización, recodificación y reestructuración de los misiles interplanetarios, ademas de solucionar seberos bugs.-
- Solo se permiten caracteres alfanumericos en el nombre de los planetas, evita serios bugs y filtros de seguridad.-
- Arreglado el orden por puntos en la alianza.-
- Tutorial funcionando.-
- Correcciones en el mensaje de bienvenida pos-registro.-
- Solucionado el bug que no permitía la transferencia de la alianza.-
- Solucionado el bug que hace que salga el rango equivocado al usuario en la lista de miembros de la ally.-
- Solucionado el bug que permitía que se envien solicitudes una vez que la alianza habia sido borrada.-
- Reparada la red de investigación intergaláctica.-
- Cupula y protector planetario funcionando, y cada una solo puede ser edificada una vez.-
',

'1.1c' => ' 19/11/08

- Cambios en la organización de la carpeta templates.-
- Algunos fixes en el leftmenu del admin.-
- Nuevamente reparada la seccion de de Annonces (sirve para comerciar).-
- Volvimos a implementar el leftmenu antigüo, funciona más rápido.-
- Mejoras en algunas traducciones, y añadidas otras.-
- Añadida la hora al chat. [Aún no funciona en hostings].-
- Limpieza de archivos inecesarios y/o sin ninguna utilidad.-
- Añadido el auto-update.-
- Eliminado el upgrade desde ugamela.-
- Mejoras en la instalación.-
',

'1.1b' => ' 30/10/08

- Añadido un tutorial, desarrollado por PowerMaster para el XNova de Xtreme-gameZ.com.ar.-
- Cambios de nombre del archivo de instalacion "Installeur" a "Instalacion de XNova".-
- Cambios en el leftmenu para usuarios.-
- Actualizacion de Puntos Automaticamente, ahora si anda.-
- Introduccion del Release de Xtreme-GameZ en "credit.php" e "install.php".-
- Cambios de idioma de carpeta "fr" a carpeta "es" (requiere instalacion).-
',

'1.1a' => ' 28/10/08

- Antes, si mandaban una flota y cambian de planeta, tiraba error.-
- Antes, cuando estaban leyendo mensajes y cambian de planeta, tiraba error.-
- Ahora al cancelar una investigación te devuelve los recursos.-
- Cambio en el texto del primer mensaje recibido al registrarse en el juego.-
- Agregadas las estadísticas de batalla.-
- Fueron agregadas las defensas al ranking de la Visión General.-
',


'1.0b' => ' 26/10/08

- Primer release disponible para los usuarios.-
- Eliminado el warning que aparecía en la instalación del sistema.-
- El instalador ahora incluye la actualización de puntos automática, por ende el usuario ya no debera tocar nada en el código.-
- Aplicada la actualización automática de puntos.-
',


'1.0a' => ' 24/10/08 "Versión Inicial"

- Cambios de lenguaje en el changelog (100% traducido).-
- Mejora del menú de la izquierda se "visualiza" algo mejor.-
- Correciones de lenguaje en el install (install.mo).-
- Correciones en el Marchand (Mercader), ya esta funcionando correctamente, no tira más ese error del lenguaje.-
- Fixes en el link de Annonces, ahora esta funcionando, ya puedes publicar lo que desees comercias.-
- Inicio del proyecto XG (XG Proyect) basandonos en el pack hecho por XNova versión 0.9a.-
',
);
?>