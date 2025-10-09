@include('layouts.navbars.main-navbar')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-body p-5">
                <h1 class="card-title h2 mb-4">Contactez-nous</h1>
                    <div class="container py-5">
                        <h1 class="text-center mb-4">Contactez-nous</h1>
                        <form>
                            <div class="mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea class="form-control" rows="4"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Envoyer</button>
                        </form>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection

