<template>
    <div class="formstack-docs">
        <div class="docs-links">
            <div class="links-group">
                <h4 class="group-title">Documents</h4>
                <div class="link" data-url="documents/ALL">
                    <p class="url">GET /api/documents</p>
                    <p class="description">Get All Documents</p>
                </div>
                <div class="link" data-url="documents/POST">
                    <p class="url">POST /api/documents</p>
                    <p class="description">Create Document</p>
                </div>
                <div class="link" data-url="documents/GET">
                    <p class="url">GET /api/documents/{id}</p>
                    <p class="description">Get Specific Document</p>
                </div>
                <div class="link" data-url="documents/PUT">
                    <p class="url">PUT /api/documents/{id}</p>
                    <p class="description">Update Document</p>
                </div>
                <div class="link" data-url="documents/DELETE">
                    <p class="url">DELETE /api/documents/{id}</p>
                    <p class="description">Delete Document</p>
                </div>
            </div>

            <div class="links-group">
                <h4 class="group-title">Document Data Points</h4>
                <div class="link" data-url="documents/data/GET">
                    <p class="url">GET /api/documents/{id}/data/{key}</p>
                    <p class="description">Get Document Data Point</p>
                </div>
                <div class="link" data-url="documents/data/PATCH">
                    <p class="url">PATCH /api/documents/{id}/data/{key}</p>
                    <p class="description">Update Document Data Point</p>
                </div>
                <div class="link" data-url="documents/data/DELTE">
                    <p class="url">DELETE /api/documents/{id}/data/{key}</p>
                    <p class="description">Delete Document Data Point</p>
                </div>
            </div>

            <div class="links-group">
                <h4 class="group-title">Document Exports</h4>
                <div class="link" data-url="documents/export/GET">
                    <p class="url">GET /api/documents/{id}/export</p>
                    <p class="description">Download Document</p>
                </div>
                <div class="link" data-url="documents/export/POST">
                    <p class="url">POST /api/documents/{id}/export</p>
                    <p class="description">Export Document</p>
                </div>
            </div>
        </div>

        <div class="placeholder">
            Formstack<br>
            Challenge
        </div>

        <div class="docs-area markdown-body" id="docs-area"></div>
    </div>
</template>

<script>
    export default {
        mounted() {
            $('.docs-area').hide();
            $(document).on('click', '.link', function(event) {
                $('.link.active').removeClass('active');
                $(this).addClass('active');
                $('.docs-area').html('').hide();
                $('.placeholder').html('<i class="fas fa-spinner fa-spin"></i>').show();

                $.ajax({
                    url: '/doc',
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {doc: $(this).data('url')},
                })
                .done(function(data) {
                    $('.placeholder').html('').hide();
                    $('.docs-area').html(marked(data)).show();
                })
                .fail(function() {
                    console.log("error");
                })
                .always(function() {
                    console.log("complete");
                });

            });
        },
    }
</script>
