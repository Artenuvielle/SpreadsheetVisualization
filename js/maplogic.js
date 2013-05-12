
    /**
     * @copyright Copyright (C) René Martin, 2012. All rights reserved.
     * @license   GNU General Public License version 2 or later; see LICENSE.txt
     **/

var fromProjection = new OpenLayers.Projection("EPSG:4326"); // Transform from WGS 1984
var toProjection = new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection
var map, normalicon, inactiveicon, activeicon, markers, disabledmarkers, activemarkers, layerswitcher;


$(window).load(function(){
        // Set map size
        resizeMap();

        // Initialize map and marker layers
        map = new OpenLayers.Map("actualMap");
        var mapnik = new OpenLayers.Layer.OSM();
        map.addLayer(mapnik);
        markers = new OpenLayers.Layer.Markers( "Alle Objekte (rot)" );
        map.addLayer(markers);
        disabledmarkers = new OpenLayers.Layer.Markers( "Unpassende Objekte (gelb)" );
        map.addLayer(disabledmarkers);
        activemarkers = new OpenLayers.Layer.Markers( "ausgewähltes Objekt (blau)" );
        map.addLayer(activemarkers);
        layerswitcher = new OpenLayers.Control.LayerSwitcher();
        map.addControl(layerswitcher);

        // Load all marker icons
        var size = new OpenLayers.Size(21,25);
        var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
        normalicon = new OpenLayers.Icon('js/openlayers/img/marker.png', size, offset);
        inactiveicon = new OpenLayers.Icon('js/openlayers/img/marker-gold.png', size, offset);
        activeicon = new OpenLayers.Icon('js/openlayers/img/marker-blue.png', size, offset);
        allmarkers = Array();

        // Initialize map view
        zoomMapTo(10.208496093692, 51.280776642105, 5);
});

function zoomMapTo(lon, lat, zoom) {
        // Zoom to position with given zoom state
        var position = new OpenLayers.LonLat(lon, lat).transform( fromProjection, toProjection);
        map.setCenter(position, zoom);
}

function zoomMapToBounds(lonmin, lonmax, latmin, latmax) {
        // Zoom the map that the rectangle defined is definately included
        var bounds = new OpenLayers.Bounds();
        bounds.extend(new OpenLayers.LonLat(lonmin, latmin).transform( fromProjection, toProjection));
        bounds.extend(new OpenLayers.LonLat(lonmax, latmax).transform( fromProjection, toProjection));
        map.zoomToExtent(bounds);
}

function addMarkerAt(lon, lat) {
        // Adds normal marker
        markers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(lon, lat).transform( fromProjection, toProjection), normalicon.clone()));
}

function addInactiveMarkerAt(lon, lat) {
        // Adds inactive marker
        disabledmarkers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(lon, lat).transform( fromProjection, toProjection), inactiveicon.clone()));
}

function addActiveMarkerAt(lon, lat) {
        // Resets active marker layer and adds active marker
        activemarkers = resetlayer(activemarkers, "ausgewähltes Objekt (blau)" );
        activemarkers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(lon, lat).transform( fromProjection, toProjection), activeicon.clone()));
}

function resetMarkers() {
        // Resets all marker layers
        markers = resetlayer(markers, "Alle Objekte (rot)");
        disabledmarkers = resetlayer(disabledmarkers, "Unpassende Objekte (gelb)" );
        activemarkers = resetlayer(activemarkers, "ausgewähltes Objekt (blau)" );
        allmarkers = Array();
}

function resetlayer(layertoreset, textforlayer) {
        // Resets given layer
        map.removeLayer(layertoreset);
        layertoreset.destroy();
        layertoreset = new OpenLayers.Layer.Markers(  );
        map.addLayer(layertoreset);
        return layertoreset;
}

$(window).resize(resizeMap);

function resizeMap() {
        // Upon resizing the window update also map size
        if($("body").width() > 767)
        {
                var navbarh = $('#mapHolder').position().top;
                var footh = $('#footer').height();
                var bodyh = $("body").height();
                $('#mapHolder').css("height",(bodyh-(navbarh+footh+40))+"px");
        } else {
                $('#mapHolder').css("height","400px");
        }
}