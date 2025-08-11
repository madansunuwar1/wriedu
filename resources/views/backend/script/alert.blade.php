<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WRI Education Consultancy - Alerts</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .swal-custom-popup {
            width: 90% !important;
            max-width: 400px !important;
            padding: 15px !important;
            font-size: 1rem !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
        }

        .swal-custom-title {
            font-size: 1.4rem !important;
            font-weight: 600 !important;
            margin-bottom: 10px !important;
            color: #333 !important;
        }

        .swal-custom-content {
            font-size: 1rem !important;
            line-height: 1.5 !important;
            margin-bottom: 12px !important;
            color: #555 !important;
        }

        .swal-custom-button {
            color: white !important;
            border: none;
            padding: 10px 20px !important;
            border-radius: 5px !important;
            font-size: 0.9rem !important;
            font-weight: 500 !important;
            margin: 8px !important;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
        }

        .swal-custom-button:focus {
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25) !important;
            outline: none;
        }

        .swal-custom-ok-button {
            background: linear-gradient(145deg, #34ca5e, #28a745) !important;
            border: 1px solid #249c3f !important;
        }

        .swal-custom-ok-button:hover {
            background: linear-gradient(145deg, #28a745, #218838) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 3px 6px rgba(0,0,0,0.15) !important;
        }

        .swal-custom-delete-button {
            background-color: #dc3545 !important;
        }

        .swal-custom-delete-button:hover {
            background-color: #c82333 !important;
            transform: translateY(-1px) !important;
        }

        .swal-custom-edit-button {
            background-color: #007bff !important;
        }

        .swal-custom-edit-button:hover {
            background-color: #0056b3 !important;
            transform: translateY(-1px) !important;
        }

        .swal-custom-update-button {
            background-color: #17a2b8 !important;
        }

        .swal-custom-update-button:hover {
            background-color: #138496 !important;
            transform: translateY(-1px) !important;
        }

        .swal-custom-reminder-button {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        .swal-custom-reminder-button:hover {
            background-color: #e0a800 !important;
            transform: translateY(-1px) !important;
        }

        .swal-custom-cancel-button {
            background-color: #6c757d !important;
        }

        .swal-custom-cancel-button:hover {
            background-color: #5a6268 !important;
            transform: translateY(-1px) !important;
        }

        .swal2-html-container {
            margin: 0.7em 1.6em 0.2em !important;
        }

        .swal2-actions {
            margin: 1em auto 0 !important;
            gap: 0.7em !important;
        }

        @media (max-width: 600px) {
            .swal-custom-popup {
                width: 95% !important;
                padding: 10px !important;
            }

            .swal-custom-title {
                font-size: 1.2rem !important;
            }

            .swal-custom-content {
                font-size: 0.9rem !important;
            }

            .swal-custom-button {
                padding: 8px 16px !important;
                font-size: 0.8rem !important;
            }
        }
    </style>
</head>
<body>

  

    <script>
        function showAlert(icon, title, text, confirmButtonText = 'OK', customConfirmClass = 'swal-custom-ok-button') {
            Swal.fire({
                icon: icon,
                title: title,
                text: text,
                confirmButtonText: confirmButtonText,
                customClass: {
                    popup: 'swal-custom-popup',
                    title: 'swal-custom-title',
                    htmlContainer: 'swal-custom-content',
                    confirmButton: `swal-custom-button ${customConfirmClass}`
                },
                buttonsStyling: false
            });
        }

        function showConfirmation(
            title,
            text,
            confirmButtonText,
            cancelButtonText,
            confirmAction,
            cancelAction = () => {},
            icon = 'warning',
            customConfirmClass = 'swal-custom-ok-button',
            customCancelClass = 'swal-custom-cancel-button'
        ) {
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonText: confirmButtonText,
                cancelButtonText: cancelButtonText,
                reverseButtons: true,
                customClass: {
                    popup: 'swal-custom-popup',
                    title: 'swal-custom-title',
                    htmlContainer: 'swal-custom-content',
                    confirmButton: `swal-custom-button ${customConfirmClass}`,
                    cancelButton: `swal-custom-button ${customCancelClass}`
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    if (typeof confirmAction === 'function') {
                        confirmAction();
                    }
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    if (typeof cancelAction === 'function') {
                        cancelAction();
                    }
                }
            });
        }

        function triggerDeleteExample(itemId) {
            showConfirmation(
                'Confirm Deletion',
                `Are you sure you want to delete item #${itemId}? This cannot be undone.`,
                'Yes, Delete It',
                'Cancel',
                () => {
                    showAlert('success', 'Deleted!', `Item #${itemId} has been deleted (simulation).`);
                },
                () => {
                    showAlert('info', 'Cancelled', 'Deletion was cancelled.');
                },
                'warning',
                'swal-custom-delete-button'
            );
        }
    </script>

    
</body>
</html>
