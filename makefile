# ==========================
#  Dist (zip sanitizado)
# ==========================
PROJECT       ?= erp
DIST_DIR      ?= dist
# Versão: usa tag/commit se houver git; senão, timestamp
VERSION       := $(shell git describe --tags --always --dirty 2>/dev/null || date +%Y%m%d-%H%M%S)
DIST_NAME     := $(PROJECT)-$(VERSION).zip
DIST_PATH     := $(DIST_DIR)/$(DIST_NAME)

# Padrões de EXCLUSÃO (não entram no zip de distribuição)
DIST_EXCLUDES = \
  vendor/* \
  node_modules/* \
  dist/* \
  build/* \
  coverage/* \
  storage/* \
  logs/* \
  runtime/* \
  tmp/* .tmp/* \
  .git/* .gitignore \
  .vscode/* .idea/* \
  .DS_Store Thumbs.db \
  *.log *.pid \
  .env .env.* \
  *.secret *.key \
  docker-compose.yml

# Lista de exclusões em formato -x para o zip
ZIP_EXCLUDE_FLAGS = $(foreach p,$(DIST_EXCLUDES),-x "$(p)")

.PHONY: dist dist-clean show-dist

dist:
	@mkdir -p "$(DIST_DIR)"
	@echo ">> Criando $(DIST_PATH)"
	@zip -rq "$(DIST_PATH)" . $(ZIP_EXCLUDE_FLAGS)
	@shasum -a 256 "$(DIST_PATH)" > "$(DIST_PATH).sha256"
	@echo "OK: $(DIST_PATH)"
	@$(MAKE) show-dist

show-dist:
	@ls -lh "$(DIST_PATH)"*
	@echo ""
	@echo "Conteúdo (topo):"
	@zipinfo -1 "$(DIST_PATH)" | head -n 30

dist-clean:
	@rm -rf "$(DIST_DIR)"
	@echo "Limpo: $(DIST_DIR)"

# Utilitário opcional: imprime a versão detectada
version:
	@echo "$(VERSION)"
