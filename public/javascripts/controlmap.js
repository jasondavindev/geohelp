gMap = {
  initMap: () => {
    this.map = new google.maps.Map(document.getElementById('map'), {
      zoom: 15,
      center: { lat: -23.235203, lng: -45.915197 },
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
  },
  initMapEdit: () => {
    this.mapEdit = new google.maps.Map(document.getElementById('edit_map'), {
      zoom: 15,
      center: {lat: -23.235203, lng: -45.915197},
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    const drawingManager = new google.maps.drawing.DrawingManager({
      drawingMode: google.maps.drawing.OverlayType.POLYLINE,
      drawingControl: true,
      drawingControlOptions: {
        position: google.maps.ControlPosition.TOP_CENTER,
        drawingModes: [
          google.maps.drawing.OverlayType.POLYLINE
        ]
      }
    });
    drawingManager.setMap(this.mapEdit);

    google.maps.event.addListener(drawingManager, 'overlaycomplete', (event) => {
      if (event.type == google.maps.drawing.OverlayType.POLYLINE) {

        var drawn = event.overlay;
        drawingManager.setOptions({drawingMode: null});

        var len = drawn.getPath().getLength();

        var str = '';

        for(var i = 0; i < len; i++)
        {
          str += drawn.getPath().getAt(i).toUrlValue(5)+',';
        }
      }
      savePoligon(str);
    });
  },
  setPoligon: (poligon) => {
    poligon.setMap(this.map);
  },
  blocks: [],
  getBlocks: () => {
    return this.blocks
  },
  setBlock: (block) => {
    this.blocks = block
    return this.blocks;
  },
};

initMap = () => {
  gMap.initMap();
};
