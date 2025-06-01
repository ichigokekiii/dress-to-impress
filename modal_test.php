<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal Test</title>
    <style>
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
        }
        .modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 5px;
            display: none;
        }
        .modal.show {
            display: block;
        }
        .modal-backdrop.show {
            display: block;
        }
        .btn {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-secondary {
            background: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container" style="padding: 20px;">
        <button onclick="document.getElementById('testModal').classList.add('show'); document.getElementById('modalBackdrop').classList.add('show')">
            Open Modal
        </button>

        <div class="modal-backdrop" id="modalBackdrop" onclick="this.classList.remove('show'); document.getElementById('testModal').classList.remove('show')"></div>
        
        <div class="modal" id="testModal">
            <h3>Test Modal</h3>
            <p>If you can see this, the modal is working!</p>
            <button class="btn btn-secondary" onclick="document.getElementById('testModal').classList.remove('show'); document.getElementById('modalBackdrop').classList.remove('show')">
                Close
            </button>
        </div>
    </div>
</body>
</html> 