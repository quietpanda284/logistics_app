<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter New Jobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Logistics Co.</a>
            
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="enter_job.php">Create Job</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_sites.php">Manage Sites</a></li>
                    <li class="nav-item"><a class="nav-link" href="search_jobs.php">Search Jobs</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="row justify-content-center mt-5">
    <div class="col-md-8">
        <div class="card shadow bg-dark text-white border-secondary">
            <div class="card-header border-secondary">
                <h4 class="pt-2">Create New Job</h4>
            </div>
            <div class="card-body">
                <form action="insert_job_logic.php" method="POST">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Good Name</label>
                            <input type="text" name="goodname" class="form-control bg-secondary text-white border-0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Good Quantity</label>
                            <input type="number" name="quantity" class="form-control bg-secondary text-white border-0" required>
                        </div>
                    </div>
                    

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" name="weight" class="form-control bg-secondary text-white border-0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Size (m³)</label>
                            <input type="number" name="size" class="form-control bg-secondary text-white border-0" required>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="hazardous" class="form-check-input" id="hazCheck">
                        <label class="form-check-label text-warning" for="hazCheck">Hazardous</label>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Starting Site</label>
                            <select class="form-select bg-secondary text-white border-0">
                                <option value="London_Depot">London - Heathrow Distribution Centre</option>
                                <option value="Manchester_Hub">Manchester - Trafford Logistics Hub</option>
                                <option value="Birmingham_NDC">Birmingham - National Distribution Centre</option>
                                <option value="Liverpool_HQ">Liverpool - Company Headquarters & Main Depot</option>
                                <option value="Glasgow_Port">Glasgow - Clydeside Freight Port</option>
                                <option value="Cardiff_Warehouse">Cardiff - South Wales Storage Facility</option>
                                <option value="Bristol_West">Bristol - Western Regional Warehouse</option>
                                <option value="Leeds_North">Leeds - Northern Consolidation Point</option>
                                <option value="Southampton_Docks">Southampton - Deep Sea Docks Terminal</option>
                                <option value="Newcastle_Tyne">Newcastle - Tyne River Distribution Point</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Destination</label>
                            <select class="form-select bg-secondary text-white border-0">
                                <option value="London_Depot">London - Heathrow Distribution Centre</option>
                                <option value="Manchester_Hub">Manchester - Trafford Logistics Hub</option>
                                <option value="Birmingham_NDC">Birmingham - National Distribution Centre</option>
                                <option value="Liverpool_HQ">Liverpool - Company Headquarters & Main Depot</option>
                                <option value="Glasgow_Port">Glasgow - Clydeside Freight Port</option>
                                <option value="Cardiff_Warehouse">Cardiff - South Wales Storage Facility</option>
                                <option value="Bristol_West">Bristol - Western Regional Warehouse</option>
                                <option value="Leeds_North">Leeds - Northern Consolidation Point</option>
                                <option value="Southampton_Docks">Southampton - Deep Sea Docks Terminal</option>
                                <option value="Newcastle_Tyne">Newcastle - Tyne River Distribution Point</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control bg-secondary text-white border-0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Deadline</label>
                            <input type="date" name="deadline" class="form-control bg-secondary text-white border-0" required>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-2">
                        <button type="submit" class="btn btn-primary">Submit Job</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>