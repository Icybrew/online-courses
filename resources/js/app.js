/* Styles */
import '../css/style.scss';

/* Javascript */
import $ from 'jquery/dist/jquery.slim.min.js';
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
    this.className = 'draggable-drop-container';
}

function onDrop() {
    this.className = 'draggable-drop-container';

    // Checking if moving within same container or new
    if (this.parentNode === current_draggable.parentNode.parentNode) {
        console.log('Old container movement');

        if (this.children.length > 0) {

            // Preventing movement on self container
            this.childNodes.forEach(node => {
                if (node === current_draggable) {
                    return null;
                }
            });

            console.log('');

        } else {
            this.append(current_draggable);
        }
    } else {
        console.log('New container movement');

        // Checking if container contains anything
        if (this.children.length > 0) {

            // Checking if there's empty container in parent to append to
            for (const parentChild of this.parentNode.children) {
                if (parentChild.children.length === 0) {
                    parentChild.append(current_draggable);
                    return;
                }
            }

            // Else create new container
            const container = document.createElement('div');
            container.className = 'draggable-drop-container';

            while (this.childNodes.length > 0) {
                container.append(this.childNodes[0]);
            }

            registerContainers([container]);
            this.parentNode.append(container);
            this.append(current_draggable);

        } else {
            this.append(current_draggable);
        }
    }
}
/* Draggable's END */