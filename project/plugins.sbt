logLevel := Level.Warn

//resolvers += Classpaths.typesafeResolver

//addSbtPlugin("com.typesafe" % "sbt-mima-plugin" % "0.1.4")

//addSbtPlugin( "com.typesafe.sbtscalariform" % "sbtscalariform" % "0.5.1") 

//addSbtPlugin("com.github.mpeltonen" % "sbt-idea" % "1.1.0")

// The Typesafe repository
resolvers += "Typesafe repository" at "http://repo.typesafe.com/typesafe/releases/"

// Use the Play sbt plugin for Play projects
addSbtPlugin("play" % "sbt-plugin" % "2.1.1")
