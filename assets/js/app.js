import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router'
import Routes from './js_routes.json'
Routing.setRoutingData(Routes)
let product = document.getElementById('product')
// todo: need to get the product id
let url = Routing.generate('product_show', {id: product.dataset.product_id})
let feedback_form = document.getElementById('feedback_form')
let feedback_list = document.getElementById('feedback_list')
let product_remove_form = document.getElementById('product_remove_form')
let download_file_button = document.getElementById('download-file')
// add a new feedback to the profuct feedback
feedback_form.addEventListener('submit', function (event)
{
    // XXX: make a post ajax request
    event.preventDefault()
    new Promise(function (resolve, reject)
    {
        let xhr = new XMLHttpRequest()
        let formData = new FormData(feedback_form)
        // third argument specifies if it's an async request or a sync
        xhr.addEventListener('load', function ()
        {
            if (this.readyState === 4 ) {
                if (this.status === 200) {
                    resolve(JSON.parse(this.response))
                } else {
                    reject(this.status)
                }
            }
        })
        xhr.open("POST", url)
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')

        xhr.send(formData)
    }) // end of the promise
    .then((data) => {
        // add the new sent data to the feedback_list
        // using just vanilla js and no other frameworks
        // create the span elements
        insertToDOM(data)
    })
    .catch((error) => {
        console.error(error)
    })
})

// listen for the delete event and send the message to the back-end
// display success or error message with redirection
product_remove_form.addEventListener('submit', function (event)
{
    event.preventDefault()
    if (confirm('Are you sure you want to removed this product')) {
        new Promise(function (resolve, reject)
        {
            let remove_url = Routing.generate('product_delete', {id: product.dataset.product_id})
            let xhr = new XMLHttpRequest()
            xhr.open("DELETE", remove_url, true)
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
            xhr.addEventListener('load', function ()
            {
                if (this.readyState === 4) {
                    if (this.status === 200 && this.statusText === "OK") {
                        resolve(JSON.parse(this.response))
                    } else {
                        reject(JSON.parse(this.response))
                    }
                }
            }) // end of data load ajax
            xhr.send()
        })
        .then((response) => {
            console.log(response)
        })
        .catch((error) => {
            alert('Opps something happened, item was not removed')
            console.error(error)
        })
    } else {
        alert('Operation was canceled by the user')
    }
})

// when document is ready do stuff
document.addEventListener('DOMContentLoaded', function (event)
{
    new Promise(function (resolve, reject)
    {
        // fetch the feedback from the server-side
        let url = Routing.generate('getFeedback', {id: product.dataset.product_id})
        let xhr = new XMLHttpRequest()
        xhr.open("GET", url, true)
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')

        xhr.addEventListener('load', function ()
        {
            if (this.readyState === 4) {
                if (this.statusText === "OK" && this.status === 200) {
                    resolve(JSON.parse(this.response))
                } else {
                    reject(this.response)
                }
            }
        })
        xhr.send()
    })
        .then((response) => {
            feedback_list.children[0].remove();
            // loop throuhgt all items and append them to the feedback list
            for (var i = 0; i < response.length; i++) {
                insertToDOM(response[i])
            }
        })
        .catch((error) => {
            // notify the user of the error
            feedback_list.children[0].innerHTML = "ERROR"
            console.error(error)
        })
})

// download a file using ajax and displaying a progress bar
download_file_button.addEventListener('click', function (event)
{
    event.preventDefault()
    let progress_bar = document.querySelector('.progress-bar')
    let url = Routing.generate('download_file')
    // download the file
    let xhr = new XMLHttpRequest()
    xhr.open("GET", url, true)
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
    // what is pe? ProgressEvent
    xhr.onprogress = function (pe)
    {
        if (pe.lengthComputable)
        {
            // todo: the url is not working 404
            // update the progress bar with the current progress
            // pe.total gets the total file size => unsigned long long
            // headers not included in the filesize aka the total
            let currentProgress = (pe.loaded / pe.total) * 100 + '%'
            progress_bar.setAttribute('style', 'width: '+ currentProgress)
            // update the inner text as well
            progress_bar.innerHTML = parseFloat(currentProgress).toFixed(2) + '%'
            // calculating the percentage
            // pe.total == 100%
            // pe.loaded respresents the current progress so far
        }
    }

    xhr.onloadend = function (pe)
    {
        //when everything is done and loaded create a img and insert it in the element
        let element = createElement('img')
    }

    xhr.send()
})

function insertToDOM(data)
{
    let $span_element = createElement('span')
    let $strong_element = createElement('strong')
    let textNode = document.createTextNode(data.name + ' - ')
    $strong_element.appendChild(textNode)
    $span_element.appendChild($strong_element)

    // email span
    let $span_element_email = createElement('span')
    let textNodeEmail = document.createTextNode(data.email)
    $span_element.appendChild(textNodeEmail)

    // message paragraph
    let p = createElement('p')
    let pTextNode = document.createTextNode(data.message)
    p.appendChild(pTextNode)

    // add all elements to the feedback_list in the right order
    feedback_list.appendChild($span_element)
    feedback_list.appendChild($span_element_email)
    feedback_list.appendChild(p)
    feedback_list.appendChild(createElement('hr'))
}

function createElement(element_name)
{
    return document.createElement(element_name)
}