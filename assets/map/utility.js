//Draw Widget Circle
function initWidget(map) {
    var distanceWidget = new DistanceWidget(map);

    google.maps.event.addListener(distanceWidget, 'distance_changed', function () {
        displayInfo(distanceWidget); //Put you core filter logic here
    });

    google.maps.event.addListener(distanceWidget, 'position_changed', function () {
        displayInfo(distanceWidget); //Put you core filter logic here
    });
}

//For display center and distance
function displayInfo(widget) {
     var info = document.getElementById('divInfo');
     info.innerHTML = 'Position: ' + widget.get('position') + ', Distance (in Km): ' +
     widget.get('distance');
}

/*------------------------------------Create Distance Widget--------------------*/

      function DistanceWidget(map) {
        this.set('map', map);
        this.set('position', map.getCenter());
        //Anchored image
        var image = {
        url: 'Center.png',
        size: new google.maps.Size(24, 24), origin: new google.maps.Point(0,0),
        anchor: new google.maps.Point(12, 12)
        };

        //Center Marker
        var marker = new google.maps.Marker({
            draggable: true,
            icon: image,
            title: 'Drag to move new location!',
            raiseOnDrag: false,
        });

        marker.bindTo('map', this);
        marker.bindTo('position', this);
        //Radius Widget
        var radiusWidget = new RadiusWidget();
        radiusWidget.bindTo('map', this);
        radiusWidget.bindTo('center', this, 'position');
        this.bindTo('distance', radiusWidget);
        this.bindTo('bounds', radiusWidget);
      }

      DistanceWidget.prototype = new google.maps.MVCObject();

/*------------------------------Create Radius widget-------------------------*/

      function RadiusWidget() {
          var circleOptions = {
              fillOpacity: 0.05,
              fillColor: '#686868',
              strokeColor: '#686868',
              strokeWeight: 1,
                               strokeOpacity: 0.8
          };
    var circle = new google.maps.Circle(circleOptions);
        this.set('distance', 50);
        this.bindTo('bounds', circle);
        circle.bindTo('center', this);
        circle.bindTo('map', this);
        circle.bindTo('radius', this);
        // Add the sizer marker
        this.addSizer_();
      }

      RadiusWidget.prototype = new google.maps.MVCObject();
      //Distance has changed event handler.
      RadiusWidget.prototype.distance_changed = function() {
        this.set('radius', this.get('distance') * 1000);
      };

      //Sizer handler
      RadiusWidget.prototype.addSizer_ = function () {
        var image = {
                    url: 'Resize.png',
                    size: new google.maps.Size(24, 24),
                    origin: new google.maps.Point(0,0),
                    anchor: new google.maps.Point(12, 12)
                    };

          var sizer = new google.maps.Marker({
              draggable: true,
              icon: image,
              cursor: 'ew-resize',
              title: 'Drag to resize the cicle!',
              raiseOnDrag: false,
          });

          sizer.bindTo('map', this);
          sizer.bindTo('position', this, 'sizer_position');

          var me = this;
          google.maps.event.addListener(sizer, 'drag', function () {
              me.setDistance();
          });

          google.maps.event.addListener(sizer, 'dragend', function () {
              me.fitCircle();
          });
      };

      //Center changed handler
      RadiusWidget.prototype.center_changed = function() {
        var bounds = this.get('bounds');

        if (bounds) {
          var lng = bounds.getNorthEast().lng();
          var position = new google.maps.LatLng(this.get('center').lat(), lng);
          this.set('sizer_position', position);
        }
      };

      //Calculate Distance
      RadiusWidget.prototype.distanceBetweenPoints_ = function (p1, p2) {
          if (!p1 || !p2) {
              return 0;
          }

          var R = 6371; // Radius of the Earth in km
          var dLat = (p2.lat() - p1.lat()) * Math.PI / 180;
          var dLon = (p2.lng() - p1.lng()) * Math.PI / 180;
          var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
          Math.cos(p1.lat() * Math.PI / 180) * Math.cos(p2.lat() * Math.PI / 180) *
          Math.sin(dLon / 2) * Math.sin(dLon / 2);
          var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
          var d = R * c;

          //Limit max 50km and min half km
          if (d > 100) {
              d = 100;
          }
          if (d < 0.5) {
              d = 0.5;
          }
          return d;
      };

      //Set distance
      RadiusWidget.prototype.setDistance = function() {
        var pos = this.get('sizer_position');
        var center = this.get('center');
        var distance = this.distanceBetweenPoints_(center, pos);
        this.set('distance', distance);
         var bounds = this.get('bounds');
        if (bounds) {
          var lng = bounds.getNorthEast().lng();
          var position = new google.maps.LatLng(this.get('center').lat(), lng);
          this.set('sizer_position', position);
        }
    };

    //Fit circle when changed
    RadiusWidget.prototype.fitCircle = function () {

        var bounds = this.get('bounds');

        if (bounds) {
          map.fitBounds(bounds);

          var lng = bounds.getNorthEast().lng();
          var position = new google.maps.LatLng(this.get('center').lat(), lng);
          this.set('sizer_position', position);
        }
    };
