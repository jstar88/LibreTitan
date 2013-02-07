import sbt._
import Keys._
import play.Project._

object ApplicationBuild extends Build {

  val appName         = "libretitan"
  val appVersion      = "1.0-SNAPSHOT"

  val appDependencies = Seq(
    // Add your project dependencies here, 
    javaCore,
    javaJdbc,
    javaEbean,
    "postgresql" % "postgresql" % "8.4-702.jdbc4"
  )

  val main = play.Project(appName, appVersion, appDependencies).settings(
    // Add your own project settings here      
  )

}
