    </main>

    <footer class="bg-dark text-white py-4">
        <div class="container text-center text-md-start d-md-flex justify-content-between">
            <p class="mb-0">&copy; <?= date('Y') ?> DragonStone Collective</p>
            <div>
                <a class="text-white-50 me-3" href="#">Privacy</a>
                <a class="text-white-50" href="#">Terms</a>
            </div>
        </div>
    </footer>

    <?php $rootUrl = rtrim($baseUrl, '/'); ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= htmlspecialchars($rootUrl) ?>/assets/js/main.js"></script>
</body>
</html>
