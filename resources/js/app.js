/* Styles */
import '../css/style.scss';

/* Javascript */
//import $ from 'jquery/dist/jquery.slim.min.js';
require('jquery');
// require('webpack-jquery-ui');
// require('webpack-jquery-ui/css');
import 'popper.js/dist/popper.min.js';
import 'bootstrap/dist/js/bootstrap.min.js';

const axios = require('axios').default;

axios.defaults.baseURL = "http://localhost/online-courses/public/";

/* Input image preview */
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#inputCoverImagePreview').attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
    }
}

$('#inputCoverImage').change(function () {
    readURL(this);
});
/* Input image preview END */

/* Draggable's START */
const draggables = document.querySelectorAll('.draggable');
const dropContainers = document.querySelectorAll('.draggable-drop-container');
const containers = document.querySelectorAll('.draggable-container');

let current_draggable;

registerDraggables(draggables);
registerContainers(dropContainers);

function registerDraggables(draggables = []) {
    for (const draggable of draggables) {
        draggable.addEventListener('dragstart', onDragStart);
        draggable.addEventListener('dragend', onDragEnd);
    }
}

function registerContainers(containers = []) {
    for (const container of containers) {
        container.addEventListener('dragover', onDragOver);
        container.addEventListener('dragenter', onDragEnter);
        container.addEventListener('dragleave', onDragLeave);
        container.addEventListener('drop', onDrop);
    }
}

function swapChildren(a, b) {
    let aHolder = document.createElement('div');
    let bHolder = document.createElement('div');

    while (a.childNodes[0]) {
        aHolder.append(a.childNodes[0]);
    }
    while (b.childNodes[0]) {
        bHolder.append(b.childNodes[0]);
    }

    while (aHolder.childNodes[0]) {
        b.append(aHolder.childNodes[0]);
    }
    while (bHolder.childNodes[0]) {
        a.append(bHolder.childNodes[0]);
    }


}

// Drag functions
function onDragStart(e) {
    current_draggable = this;

    this.className += ' hold';

    setTimeout(() => {
        this.className = 'invisible'
    }, 0);


}

function onDragEnd() {
    this.className = 'draggable';
}

function onDragOver(e) {
    e.preventDefault();
}

function onDragEnter(e) {
    e.preventDefault();
    this.className += ' hovered';
}

function onDragLeave() {
    this.classList.remove('hovered');
}

function onDrop(e) {
    e.preventDefault();
    this.classList.remove('hovered');


    // Checking if moving within same container or new
    if (this.parentNode === current_draggable.parentNode.parentNode) {
        console.log('Old container movement');

        // Checking if target container not contains anything
        if (this.children.length > 0) {

            // Preventing movement on self
            this.childNodes.forEach(node => {
                if (node === current_draggable) {
                    return null;
                }
            });



            console.log(this);
            console.log(current_draggable.parentNode);
            swapChildren(this, current_draggable.parentNode);

            if (this.dataset.select !== undefined) {
                // TODO update select field
                console.log(Array.prototype.indexOf.call(this.parentNode.children, this));
            }


        } else {
            this.append(current_draggable);
        }
    } else {
        console.log('New container movement');

        const current_draggable_parent = current_draggable.parentNode;

        // Checking if we need to remove self fom hidden select element
        if (current_draggable.parentNode.dataset.select !== undefined) {
            console.log(document.querySelector('#' + current_draggable.parentNode.dataset.select));
            for (const child of document.querySelector('#' + current_draggable.parentNode.dataset.select).children) {
                console.log(child);
                if (child.getAttribute('value') === current_draggable.dataset.value) {
                    child.remove();
                    break;
                }
            }
        }

        // Checking if we need to append to hidden select element
        if (this.dataset.select !== undefined) {

            let option = document.createElement('option');
            option.setAttribute('value', current_draggable.dataset.value);
            option.setAttribute('selected', 'selected');
            option.value = current_draggable.dataset.value;

            document.querySelector('#' + this.dataset.select).append(option);
        }


        // Checking if target container contains anything
        if (this.children.length > 0) {

            // Checking if there's empty container in parent to append to
            for (const parentChild of this.parentNode.children) {
                if (parentChild.children.length === 0 && parentChild.classList.contains('draggable-drop-container')) {
                    parentChild.append(current_draggable);
                    return;
                }
            }

            // Else create new container
            const container = document.createElement('div');
            container.className = 'draggable-drop-container';
            container.setAttribute('data-temp', 'true');

            if (this.dataset.select !== undefined) {
                container.setAttribute('data-select', this.dataset.select);
            }


            while (this.childNodes.length > 0) {
                container.append(this.childNodes[0]);
            }

            registerContainers([container]);
            this.parentNode.append(container);
            this.append(current_draggable);

        } else {
            this.append(current_draggable);
        }

        if (current_draggable_parent.dataset.temp !== undefined) {
            if (current_draggable_parent.dataset.temp === 'true') {
                current_draggable_parent.remove();
            }
        }
    }
}

$('#selections').on('change', function (e) {
    var self = this,
        selected = $(this).data('selected') || [];

    $.each(selected, function (_, i) {
        $('option', self).eq(i).prop('selected', true)
    });

    $(this).data('selected', $.map($('option:selected', this), function (el) {
        return $(el).index();
    }));
});


/* Draggable's END */