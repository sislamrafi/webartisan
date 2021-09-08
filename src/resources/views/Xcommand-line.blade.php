<!DOCTYPE html>
<html>

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: #000;
            color: #0F0;
            font-family: monospace;
            height: 100vh;
            box-sizing: border-box;
            overflow-x: hidden;
            overflow-y: scroll;
            word-break: break-all;
            margin: 0;
            padding: 16px;
        }

        #input {
            display: inline;
            outline: none;
            visibility: visible;
        }

        /*
  If you press the Insert key, the vertical line caret will automatically
  be replaced by a one-character selection.
*/
        #input::selection {
            color: #000;
            background: #0F0;
        }

        #input:empty::before {
            content: ' ';
        }

        @keyframes blink {
            to {
                visibility: hidden;
            }
        }

        #input:focus+#caret {
            animation: blink 1s steps(5, start) infinite;
        }

        #input.noCaret+#caret {
            visibility: hidden;
        }

        #caret {
            border: 0;
            padding: 0;
            outline: none;
            background-color: #0F0;
            display: inline-block;
            font-family: monospace;
        }

    </style>
</head>

<body>

    <div id="history"></div>


    <div id="input" contenteditable="true"></div><button id="caret" for="input">&nbsp;</button>

</body>

<script>
    const history = document.getElementById('history');
    const input = document.getElementById('input');
    const cursor = document.getElementById('cursor');

    const panel = "admin@terminal~:";
    var commandText = '';

    function focusAndMoveCursorToTheEnd(e) {
        input.innerText=panel;
        input.focus();

        const range = document.createRange();
        const selection = window.getSelection();
        const {
            childNodes
        } = input;
        const lastChildNode = childNodes && childNodes.length - 1;

        range.selectNodeContents(lastChildNode === -1 ? input : childNodes[lastChildNode]);
        range.collapse(false);

        selection.removeAllRanges();
        selection.addRange(range);
        
    }

    function handleCommand(command, type = 'main') {
        var line;
        if (type == 'main'){
            line = document.createElement('DIV');
            line.textContent = panel+` ${ command }`;
        }
        if (type == 'result'){
            line = document.createElement('DIV');
            line.innerHTML = command;
        }

        history.appendChild(line);
        window.scrollTo(0, document.body.scrollHeight);
    }

    function clearHistory() {
        history.innerHTML = "";
    }

    // Every time the selection changes, add or remove the .noCursor
    // class to show or hide, respectively, the bug square cursor.
    // Note this function could also be used to enforce showing always
    // a big square cursor by always selecting 1 chracter from the current
    // cursor position, unless it's already at the end, in which case the
    // #cursor element should be displayed instead.
    document.addEventListener('selectionchange', () => {
        if (document.activeElement.id !== 'input') return;

        const range = window.getSelection().getRangeAt(0);
        const start = range.startOffset;
        const end = range.endOffset;
        const length = input.textContent.length;

        if (end < length) {
            input.classList.add('noCaret');
        } else {
            input.classList.remove('noCaret');
        }
    });

    input.addEventListener('input', () => {
        // If we paste HTML, format it as plain text and break it up
        // input individual lines/commands:
        if (input.childElementCount > 0) {
            const lines = input.innerText.replace(/\n$/, '').split('\n');
            const lastLine = lines[lines.length - 1];

            for (let i = 0; i <= lines.length - 2; ++i) {
                handleCommand(lines[i]);
            }

            input.textContent = lastLine;

            focusAndMoveCursorToTheEnd();
        }

        // If we delete everything, display the square caret again:
        if (input.innerText.length === 0) {
            input.classList.remove('noCaret');
        }
    });

    document.addEventListener('keydown', (e) => {
        // If some key is pressed outside the input, focus it and move the cursor
        // to the end:
        if (e.target !== input) focusAndMoveCursorToTheEnd();
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            commandText = input.textContent.substring(panel.length);


            if (commandText == 'cls') {
                clearHistory();
                input.textContent = '';
                focusAndMoveCursorToTheEnd();
                return;
            }


            if (commandText != '') {
                console.log("Request server for artisan");
                xhr.open("POST", url, true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.send(JSON.stringify({
                    _token: csrf,
                    command: commandText
                }));
            } else {
                input.textContent = '';
                focusAndMoveCursorToTheEnd();
            }
        }
    });

    // Set the focus to the input so that you can start typing straigh away:
    focusAndMoveCursorToTheEnd();


    var url = "{{ route('artisan.submit') }}";
    var xhr = new XMLHttpRequest();
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    //xhr.open("POST", url, true);
    //xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onload = function() {
        console.log("HELLO");
        console.log(this.responseText);
        //var data = JSON.parse(this.responseText);

        data = this.responseText;

        if(data.indexOf("<!doctype html>")>0)
            data = data.substring(0 , data.indexOf("<!doctype html>"));

        
        console.log(data);

        input.textContent = commandText + ' :executed | code:' + this.status;

        handleCommand(input.textContent);
        input.textContent = '';


        //var inputArray = data.split('\n');

        //inputArray.forEach(element => {
        //    handleCommand(element, 'result');
        //});

        handleCommand(data, 'result');

        focusAndMoveCursorToTheEnd();
    }
</script>

</html>
