$(function() {
  const fns = {
    getThemes: () => {
      $.get("?route=themes", '',
        function (data, textStatus, jqXHR) {
          if (jqXHR.status === 200) {
            const themes = data;

            for (const i of themes) {
              const template = `<li id=${i.old}>${i.new}</li>`;
              $('#themes').append(template);
            }
            if (data.length > 0) $('#themes li')[0].click();
          }
        }, "json");
    },

    getBlocks: () => {
      $.get("?route=getblocks", '',
        function (data, textStatus, jqXHR) {
          if (jqXHR.status === 200) {
            let blocks = [];

            let html = '';
            const regex = /^id_/i;

            for (const elem of data) {
              const poligon = elem.poligon.split(',');
              const path = [];

              do {
                path.push({lat: parseFloat(poligon[0]), lng: parseFloat(poligon[1])});
                poligon.splice(0, 2);
              } while (poligon.length > 0);

              blocks[elem.id] = {
                poligon: new google.maps.Polygon({
                  paths: path,
                  strokeColor: '#ffffff',
                  strokeOpacity: 1.0,
                  strokeWeight: 2,
                  fillColor: '#ffffff',
                  fillOpacity: 0.50
                }),
              }
              Object.defineProperty(blocks[elem.id], 'path', { value: path, writable: true });
              gMap.setPoligon(blocks[elem.id].poligon);
              
              html += `<li>`;
              for (const i in elem) {
                if (!regex.test(i)) {
                  if (i !== 'poligon') {
                    html += `<div class="item ${i}">${elem[i]}</div>`;
                  }
                } else {
                  const name = i.replace('id_', '');
                  Object.defineProperty(blocks[elem.id], name, { value: elem[i], writable: true });

                  html += `<div class="item theme">
                      <span class="name-field">${name}</span>
                      <span title="Clique para editar" class="value-classification" data-theme="${name}" data-id="${elem['id']}" data-value="${elem[i]}">${elem[i]}</span>
                    </div>`;
                }
              }
              html += '</li>';
            }
            gMap.setBlock(blocks);
            $('#blocks').html(html);
          }
        }, "json");
    },
    tryInit: () => {
      if(window.google !== undefined) {
        fns.getBlocks();
      } else {
        setTimeout(() => {
          return fns.tryInit();
        }, 1000);
      }
    },
  }

  fns.getThemes();
  fns.tryInit();

  $(document).on('click', '#themes li', function(e) {
    const name = $(this).attr('id');
    $.get("?route=clfs", { name },
        (data, textStatus, jqXHR) => {
        if (jqXHR.status === 200) {
          $('#name-classification').text(`(${$(this).text()})`);

          // update map
          const blocks = [];

          const mapBlocks = gMap.getBlocks();
          const prop = name.replace('tp_', '');

          for (let i = 0; i < mapBlocks.length; i++) {
            const block = mapBlocks[i];

            if (block) {
              block.poligon.setMap(null);
              blocks[i] = block;
              const color = blocks[i][prop] ? `#${data[blocks[i][prop]-1].color}` : '#ffffff';
              blocks[i].poligon = new google.maps.Polygon({
                paths: block.path,
                strokeColor: color,
                strokeOpacity: 1.0,
                strokeWeight: 2,
                fillColor: color,
                fillOpacity: 0.50
              });
              gMap.setPoligon(blocks[i].poligon);
            }
          }
          gMap.setBlock(blocks);

          let html = '';

          for (const i of data) {
            html += `<li>
              <div class="cell id-class">${i.id}</div>
              <div class="cell name-class">${i.interval}</div>
              <div class="cell color-class" style="background-color: #${i.color}"></div>
            </li>`
          }

          $('#classifications-theme').html(html);
        }
      }, "json");
  });

  const itemTemplate = `
    <div class="item">
      <input type="text" class="input-text" name="name-classification[]" placeholder="Nome da classificação" required>
      <input type="color" class="input-color" name="color-classification[]">
    </div>
  `;

  $('#btn-add-theme').click(() => {
    const template = `
    <div class="modal">
      <div class="modal-content">
        <button id="close-modal">X</button>
        <div class="container">
          <div class="form-modal">
            <h2>Adicionar novo tema</h2>
            <div class="add-theme">
              <form id="form-add-theme">
                <input class="input-text" name="theme-name" type="text" placeholder="Nome do tema" required>
                <div id="item-from-form">
                  ${itemTemplate}
                </div>
                <div class="buttons-act">
                  <button class="btn bg-white" id="add-new-item-theme">+Item</button>
                  <button class="btn bg-white" id="save-theme">Salvar</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    `;
    $('#content-flex').html(template);
  });

  $(document).on('click', '.modal #close-modal', function (e) {
    e.preventDefault();
    e.stopPropagation();
    $('#content-flex').html('');
  });

  $(document).on('click', '#add-new-item-theme', function (e) {
    e.preventDefault();
    e.stopPropagation();
    $('#item-from-form').append(itemTemplate);
  });

  $(document).on('submit', '#form-add-theme', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const formData = new FormData(this);
    
    $.ajax({
      type: "POST",
      url: "?route=createtheme",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (response.saved) {
          location.reload();
        }
      }
    });
  });

  $('#btn-edit-map').click(() => {
    $('#map').toggle();
    $('#edit_map').toggle();
    gMap.initMapEdit();
  });

  $(document).on('click', '#blocks .value-classification', function(e) {
    $(this).attr('contentEditable', true);
    $(this).focus();
  });
  $(document).on('blur', '#blocks .value-classification', function(e) {
    if (parseInt($(this).text())) {
      $(this).attr('contentEditable', false).attr('data-value', $(this).text());

      $.post("?route=editblock", {
        id: $(this).attr('data-id'),
        value: $(this).attr('data-value'),
        field: $(this).attr('data-theme')
      },
        function (data, textStatus, jqXHR) {
          
        },
        "json"
      );
    } else {
      console.log('Insira um numero');
    }
  });
});

function savePoligon(path) {
 $.post("?route=saveblock", { path },
   function (data, textStatus, jqXHR) {
     console.log(data);
   },
   "json"
 );
}