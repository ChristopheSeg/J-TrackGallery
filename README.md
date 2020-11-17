### J!TrackGallery

J!TrackGallery is a Joomla GPS Track Gallery component.  
Our Goal is to provide a non commercial, free, open source, GNU/GPL3 licensed component for Joomla 3.x (with some backward compatibility), which allows
 to seamlessly upload GPX tracks and display maps and tracks on Joomla based website.
- J!Track Gallery documentation: https://mastervanleeuwen.github.io/J-TrackGallery/
- For bug reports and enhancement requests, please use the [github issues page](https://github.com/mastervanleeuwen/J-TrackGallery/issues)

#### Technical notes

J!TrackGallery uses:
- OpenLayers for maps and GPS track drawing
- Highslide to display (geotagged) photos
- The 'gd' PHP package to perform image operations like thumbnail creation for photos

#### Status of the Project

0.9.28 (15 November 2020) is an inofficial release of J!Track Gallery; the main changes with respect to 0.9.27 are:
- Move to OpenLayers v6. This changes the appearance of maps, and makes future additions easier
- Switching between maps in the front end (user view) is not supported by the new openlayers, and has been disabled
- Support for Route information from Garmin Basecamp
- Google maps support is not present by default; the terms and conditions no longer allow to display Google Maps with the OpenLayers API. There is a workaround for this, but it has not been implemented in Joomla Track Gallery yet.

0.9.27 version of J!Track Gallery have been pushed in March 2017.  
Last months commits were mostly related to adding new feature and bug fix.

#### History of J!TrackGallery  

The original versions of J!TrackGallery were written by Christoph Seguinot and published on the jtrackgallery.net website. The website recently disappeared; new versions are hosted on Github.
J!TrackGallery may be considered as a fork of [InJooosm](http://injooosm.sourceforge.net/)
This valuable component is no longer maintained (last version is for Joomla 1.5). At the beginning of this project, I was not able to reach previous author so I decided to rename the component from InJooOSM to J!TrackGallery. 

It may be note that **InJooosm** was a fork of [joomgpstracks](http://www.joomlaos.de/Joomla_CMS_Downloads/Joomla_Komponenten/JoomGPSTracks.html)

 - Many Thanks to *Michael Pfister* (JoomGPSTracks) and *Christian Knorr* (InJooOSM) for providing this valuable component to the Open source community. 

N.B. For those who always use InJooosm 0.5.7, see manual: http://jtrackgallery.net/wiki/install/migrate-from-injoosm

